import os
import re
import shutil

root_path = "dataset"
output_root = "dataset_clean"

# ==============================
# CONFIG NEGARA + NOMINAL VALID
# ==============================
configs = {
    "indonesia": {
        "currency": "Rupiah",
        "country": "Indonesia",
        "valid": [1000, 2000, 5000, 10000, 20000, 50000, 100000]
    },
    "singapore": {
        "currency": "SingaporeDollar",
        "country": "Singapore",
        "valid": [2, 5, 10, 50, 100]
    },
    "malaysia": {
        "currency": "Ringgit",
        "country": "Malaysia",
        "valid": [1, 5, 10, 20, 50, 100]
    },
    "thailand": {
        "currency": "Baht",
        "country": "Thailand",
        "valid": [20, 50, 100, 500, 1000]
    }
}

# ==============================
# 🔥 AMBIL ID TERAKHIR (LANJUT)
# ==============================
def get_last_id(folder):
    max_id = -1
    if not os.path.exists(folder):
        return 0

    for f in os.listdir(folder):
        match = re.search(r'_(\d+)\.', f)
        if match:
            num = int(match.group(1))
            max_id = max(max_id, num)

    return max_id + 1

# ==============================
# 🔥 EXTRACT NOMINAL (SUPER FLEX)
# ==============================
def extract_nominal(name, country):
    name = name.lower()

    # normalisasi typo
    name = name.replace("malasia", "malaysia")
    name = name.replace("bahttai", "baht")

    # ==========================
    # INDONESIA
    # ==========================
    if country == "indonesia":

        # 1000IndonesianRupiah
        match = re.search(r'(\d+)\s*indonesianrupiah', name)
        if match:
            return int(match.group(1))

        # 1000_rupiah
        match = re.search(r'(\d+)[-_]rupiah', name)
        if match:
            return int(match.group(1))

        # 2-000 → 2000
        match = re.search(r'(\d{1,3})-(\d{3})', name)
        if match:
            return int(match.group(1) + match.group(2))

        # fallback
        numbers = re.findall(r'\d+', name)
        numbers = [int(n) for n in numbers]

        for n in numbers:
            if n in configs["indonesia"]["valid"]:
                return n

        return None

    # ==========================
    # MALAYSIA
    # ==========================
    elif country == "malaysia":
        match = re.search(r'(\d+)\s*ringgit', name)
        if match:
            return int(match.group(1))

    # ==========================
    # SINGAPORE
    # ==========================
    elif country == "singapore":
        match = re.search(r'(\d+)\s*singaporedollar', name)
        if match:
            return int(match.group(1))

    # ==========================
    # THAILAND
    # ==========================
    elif country == "thailand":
        match = re.search(r'(\d+)\s*baht', name)
        if match:
            return int(match.group(1))

    return None

# ==============================
# 🔥 LOAD EXISTING (ANTI DUPLIKAT)
# ==============================
existing_files = set()

if os.path.exists(output_root):
    for root, dirs, files in os.walk(output_root):
        for f in files:
            existing_files.add(f)

print(f"🔍 Existing files: {len(existing_files)}")

# ==============================
# MAIN LOOP
# ==============================
for country_folder in os.listdir(root_path):

    country_path = os.path.join(root_path, country_folder)

    if not os.path.isdir(country_path):
        continue

    key = country_folder.lower()

    if key not in configs:
        print("⏭️ skip:", country_folder)
        continue

    config = configs[key]
    currency = config["currency"]
    country = config["country"]
    valid_nominals = config["valid"]

    print(f"\n🚀 Cleaning {country_folder.upper()}")

    output_images = os.path.join(output_root, key, "images")
    output_labels = os.path.join(output_root, key, "labels")

    os.makedirs(output_images, exist_ok=True)
    os.makedirs(output_labels, exist_ok=True)

    # 🔥 ID lanjut
    global_id = get_last_id(output_images)
    print("🔢 Start ID:", global_id)

    # ==============================
    # LOOP SUB DATASET (nested)
    # ==============================
    for dataset_name in os.listdir(country_path):

        dataset_path = os.path.join(country_path, dataset_name)

        if not os.path.isdir(dataset_path):
            continue

        print(f"   📦 {dataset_name}")

        for split in ["train", "valid"]:

            images_folder = os.path.join(dataset_path, split, "images")
            labels_folder = os.path.join(dataset_path, split, "labels")

            if not os.path.exists(images_folder):
                continue

            for file in os.listdir(images_folder):

                if not file.lower().endswith((".jpg", ".jpeg", ".png")):
                    continue

                old_img_path = os.path.join(images_folder, file)
                name, ext = os.path.splitext(file)

                # ==============================
                # 🔥 NOMINAL
                # ==============================
                nominal = extract_nominal(name, key)

                if nominal is None:
                    print("❌ skip:", file)
                    continue

                if nominal not in valid_nominals:
                    print("❌ invalid:", file)
                    continue

                # ==============================
                # 🔥 NAMA BARU
                # ==============================
                new_base = f"{nominal}_{currency}_{country}_{global_id}"
                new_img_name = new_base + ext

                if new_img_name in existing_files:
                    print(f"⏭️ duplicate: {new_img_name}")
                    continue

                new_img_path = os.path.join(output_images, new_img_name)
                new_label_path = os.path.join(output_labels, new_base + ".txt")

                shutil.copy(old_img_path, new_img_path)

                old_label = name + ".txt"
                old_label_path = os.path.join(labels_folder, old_label)

                if os.path.exists(old_label_path):
                    shutil.copy(old_label_path, new_label_path)
                else:
                    print("⚠️ no label:", file)

                existing_files.add(new_img_name)
                global_id += 1

                print(f"✔ {file} → {new_img_name}")

print("\n🎉 DONE! Semua dataset clean + no duplicate + siap training 🚀")