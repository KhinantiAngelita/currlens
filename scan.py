import os
import yaml
from collections import Counter

# ==============================
# CONFIG
# ==============================
BASE_PATH = "dataset_clean"
YAML_PATH = "multi_currency.yml"

# ==============================
# LOAD CLASS NAMES
# ==============================
def load_class_names(yaml_path):
    with open(yaml_path, "r") as f:
        data = yaml.safe_load(f)

    names = data.get("names", {})

    if isinstance(names, dict):
        names = [names[i] for i in range(len(names))]

    return names

# ==============================
# SCAN DATASET
# ==============================
def scan_dataset(base_path, class_names):
    total_counter = Counter()

    print("🚀 Scanning dataset...\n")

    for country in os.listdir(base_path):
        country_path = os.path.join(base_path, country)

        if not os.path.isdir(country_path):
            continue

        label_path = os.path.join(country_path, "labels")

        if not os.path.exists(label_path):
            print(f"❌ {country} tidak punya folder labels")
            continue

        print(f"🌍 COUNTRY: {country.upper()}")

        country_counter = Counter()

        for file in os.listdir(label_path):
            if not file.endswith(".txt"):
                continue

            with open(os.path.join(label_path, file)) as f:
                for line in f:
                    cls = int(line.split()[0])
                    country_counter[cls] += 1
                    total_counter[cls] += 1

        for cls, count in country_counter.items():
            name = class_names[cls] if cls < len(class_names) else f"class_{cls}"
            print(f"   {name:<30} : {count}")

        print("-" * 40)

    return total_counter

# ==============================
# ANALISIS
# ==============================
def analyze(counter, class_names):
    print("\n📊 TOTAL DISTRIBUTION\n")

    total = sum(counter.values())

    if total == 0:
        print("❌ Dataset kosong!")
        return

    for cls, count in counter.items():
        name = class_names[cls] if cls < len(class_names) else f"class_{cls}"
        percent = (count / total) * 100
        print(f"{name:<30} : {count} ({percent:.2f}%)")

    print("\n⚠️ ANALISIS IMBALANCE")

    values = list(counter.values())
    max_count = max(values)
    min_count = min(values)

    print(f"Max: {max_count}, Min: {min_count}")

    if max_count > 2 * min_count:
        print("🚨 Dataset TIDAK SEIMBANG!")
    else:
        print("✅ Dataset cukup seimbang")

# ==============================
# MAIN
# ==============================
if __name__ == "__main__":
    class_names = load_class_names(YAML_PATH)
    total_counter = scan_dataset(BASE_PATH, class_names)
    analyze(total_counter, class_names)