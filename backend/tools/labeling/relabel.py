import os
import shutil

SRC_ROOT = "dataset_clean"
DST_ROOT = "dataset_clean_relabel"

COUNTRIES = ["indonesia", "malaysia", "singapore", "thailand"]

# mapping sesuai YAML kamu
CLASS_MAP = {
    ("malaysia", "1"): 0,
    ("malaysia", "5"): 1,
    ("malaysia", "10"): 2,
    ("malaysia", "20"): 3,
    ("malaysia", "50"): 4,
    ("malaysia", "100"): 5,

    ("singapore", "2"): 6,
    ("singapore", "5"): 7,
    ("singapore", "10"): 8,
    ("singapore", "50"): 9,
    ("singapore", "100"): 10,

    ("indonesia", "1000"): 11,
    ("indonesia", "2000"): 12,
    ("indonesia", "5000"): 13,
    ("indonesia", "10000"): 14,
    ("indonesia", "20000"): 15,
    ("indonesia", "50000"): 16,
    ("indonesia", "100000"): 17,

    ("thailand", "20"): 18,
    ("thailand", "50"): 19,
    ("thailand", "100"): 20,
    ("thailand", "500"): 21,
    ("thailand", "1000"): 22,
}


def extract_info(filename):
    name = filename.replace(".txt", "")
    parts = name.split("_")

    # contoh: 50_Baht_Thailand_788
    if len(parts) < 3:
        return None, None

    nominal = parts[0]
    country = parts[2].lower()

    return country, nominal


def process():
    if os.path.exists(DST_ROOT):
        print(f"⚠️ Folder {DST_ROOT} sudah ada, menghapus...")
        shutil.rmtree(DST_ROOT)

    for country in COUNTRIES:
        print(f"\n🚀 Processing {country}")

        src_img = os.path.join(SRC_ROOT, country, "images")
        src_lbl = os.path.join(SRC_ROOT, country, "labels")

        dst_img = os.path.join(DST_ROOT, country, "images")
        dst_lbl = os.path.join(DST_ROOT, country, "labels")

        os.makedirs(dst_img, exist_ok=True)
        os.makedirs(dst_lbl, exist_ok=True)

        # copy images
        for f in os.listdir(src_img):
            shutil.copy(os.path.join(src_img, f), os.path.join(dst_img, f))

        # relabel labels
        for f in os.listdir(src_lbl):
            if not f.endswith(".txt"):
                continue

            src_path = os.path.join(src_lbl, f)
            dst_path = os.path.join(dst_lbl, f)

            country_name, nominal = extract_info(f)

            if (country_name, nominal) not in CLASS_MAP:
                print(f"❌ Tidak dikenali: {f} → ({country_name}, {nominal})")
                continue

            new_class = CLASS_MAP[(country_name, nominal)]

            new_lines = []

            with open(src_path, "r") as file:
                for line in file:
                    parts = line.strip().split()

                    if len(parts) < 5:
                        print(f"⚠️ Format salah: {f}")
                        continue

                    parts[0] = str(new_class)
                    new_lines.append(" ".join(parts))

            with open(dst_path, "w") as file:
                file.write("\n".join(new_lines))

    print("\n🎉 Relabel selesai! Dataset siap dipakai 🚀")


if __name__ == "__main__":
    process()