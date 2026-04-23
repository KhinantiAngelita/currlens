import os

BASE_PATH = "dataset_clean"

removed = 0

for country in os.listdir(BASE_PATH):
    label_path = os.path.join(BASE_PATH, country, "labels")

    if not os.path.exists(label_path):
        continue

    print(f"\n🔍 Checking: {country}")

    for file in os.listdir(label_path):
        if not file.endswith(".txt"):
            continue

        path = os.path.join(label_path, file)

        with open(path, "r") as f:
            lines = f.readlines()

        valid = True

        for line in lines:
            parts = line.strip().split()

            if len(parts) != 5:
                valid = False
                break

        if not valid or len(lines) == 0:
            print(f"❌ REMOVE: {file}")
            os.remove(path)
            removed += 1

print(f"\n🔥 Removed invalid labels: {removed}")