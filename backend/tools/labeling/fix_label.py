import os
import yaml

# ==============================
# CONFIG
# ==============================
BASE_PATH = "dataset_clean"
YAML_PATH = "multi_currency.yml"

# ==============================
# NORMALISASI NAMA
# ==============================
def normalize_currency(name):
    mapping = {
        "SingaporeDollar": "SGD",
        "Singaporedollar": "SGD",
        "Dollar": "SGD",

        "Ringgit": "Ringgit",
        "Rupiah": "Rupiah",
        "Baht": "Baht"
    }
    return mapping.get(name, name)

# ==============================
# LOAD YAML
# ==============================
with open(YAML_PATH, "r") as f:
    data = yaml.safe_load(f)

names = data["names"]

if isinstance(names, dict):
    names = [names[i] for i in range(len(names))]

name_to_id = {name: idx for idx, name in enumerate(names)}

print("\n🔍 CLASS MAPPING:")
for k, v in name_to_id.items():
    print(f"{k} → {v}")

print("\n🚀 START FIXING LABELS...\n")

fixed = 0
skipped = 0

# ==============================
# LOOP
# ==============================
for country in os.listdir(BASE_PATH):

    country_path = os.path.join(BASE_PATH, country)
    labels_path = os.path.join(country_path, "labels")

    if not os.path.exists(labels_path):
        continue

    print(f"\n🌍 {country.upper()}")

    for file in os.listdir(labels_path):

        if not file.endswith(".txt"):
            continue

        txt_path = os.path.join(labels_path, file)

        name = file.replace(".txt", "")
        parts = name.split("_")

        if len(parts) < 2:
            print(f"⚠️ skip format: {file}")
            skipped += 1
            continue

        nominal = parts[0]
        currency_raw = parts[1]

        # 🔥 NORMALISASI
        currency = normalize_currency(currency_raw)

        key = f"{nominal}_{currency}"

        if key not in name_to_id:
            print(f"⚠️ tidak ada di YAML: {file} → ({key})")
            skipped += 1
            continue

        correct_id = name_to_id[key]

        # ==============================
        # FIX LABEL
        # ==============================
        with open(txt_path, "r") as f:
            lines = f.readlines()

        new_lines = []

        for line in lines:
            parts_line = line.strip().split()

            if len(parts_line) < 5:
                continue

            parts_line[0] = str(correct_id)
            new_lines.append(" ".join(parts_line))

        with open(txt_path, "w") as f:
            f.write("\n".join(new_lines))

        print(f"✔ FIXED: {file} → {key} (class {correct_id})")
        fixed += 1

# ==============================
# SUMMARY
# ==============================
print("\n========================")
print("🎉 DONE")
print(f"✔ Fixed   : {fixed}")
print(f"⚠️ Skipped: {skipped}")
print("========================")