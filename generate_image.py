import os
import re

images_dir = "currency/train/images"
classes = set()

# daftar nominal valid (kamu bisa tambah)
valid_values = {
    1, 2, 5, 10, 20, 50, 100,
    500, 1000, 2000, 5000,
    10000, 20000, 50000, 100000,
    200000, 500000
}

for file in os.listdir(images_dir):
    name = file.lower()

    numbers = re.findall(r'\d+', name)

    for num in numbers:
        value = int(num)

        if value in valid_values:
            if "ringgit" in name:
                classes.add(f"{value} Ringgit")
            elif "rupiah" in name:
                classes.add(f"{value} Rupiah")
            elif "baht" in name:
                classes.add(f"{value} Baht")
            elif "dong" in name:
                classes.add(f"{value} Dong")
            elif "riel" in name:
                classes.add(f"{value} Riel")
            elif "peso" in name:
                classes.add(f"{value} Peso")
            elif "dollar" in name:
                classes.add(f"{value} Dollar")
            else:
                classes.add(f"{value} Unknown")

# sort
classes = sorted(classes, key=lambda x: int(x.split()[0]))

for i, c in enumerate(classes):
    print(f"{i}: {c}")