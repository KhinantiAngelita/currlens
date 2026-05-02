import os
import shutil
import random
import yaml
from tqdm import tqdm

# --- CONFIGURATION ---
DST_V3 = "data/multi_currency_gpu_v3"
CLEAN_RELABEL = "data/dataset_clean_relabel"

# Global Class Mapping (Standardized)
GLOBAL_CLASSES = [
    "myr_1", "myr_5", "myr_10", "myr_20", "myr_50", "myr_100",           # 0-5
    "sgd_2", "sgd_5", "sgd_10", "sgd_50", "sgd_100",                     # 6-10
    "idr_1000", "idr_2000", "idr_5000", "idr_10000", "idr_20000", "idr_50000", "idr_100000", # 11-17
    "thb_20", "thb_50", "thb_100", "thb_500", "thb_1000",                # 18-22
    "php_20", "php_50", "php_100", "php_200", "php_500", "php_1000"     # 23-28
]
GLOBAL_MAP = {name: i for i, name in enumerate(GLOBAL_CLASSES)}

# Raw Dataset Sources
RAW_SOURCES = [
    {
        "path": "data/dataset/thailand/Money.v2i.yolov8",
        "country": "thailand",
        "prefix": "raw_thb_",
        "map": {0: "thb_100", 1: "thb_1000", 2: "thb_20", 3: "thb_50", 4: "thb_500"}
    },
    {
        "path": "data/dataset/indonesia/Indonesia Bank Notes Dataset.v2-training-data-only.yolov8",
        "country": "indonesia",
        "prefix": "raw_idr_",
        "map": {0: "idr_1000", 1: "idr_10000", 2: "idr_100000", 3: "idr_2000", 4: "idr_20000", 5: "idr_5000", 6: "idr_50000"}
    },
    {
        "path": "data/dataset/malaysia/Ringgit Currency Detection.v2i.yolov8 (1)",
        "country": "malaysia",
        "prefix": "raw_myr_",
        "map": {0: "myr_1", 1: "myr_10", 2: "myr_100", 3: "myr_20", 4: "myr_5", 5: "myr_50"}
    }
]

def clean_and_prepare():
    if os.path.exists(DST_V3):
        print("[INFO] Removing old v3 folder...")
        shutil.rmtree(DST_V3)
    
    for split in ['train', 'val']:
        os.makedirs(os.path.join(DST_V3, 'images', split), exist_ok=True)
        os.makedirs(os.path.join(DST_V3, 'labels', split), exist_ok=True)

def remap_and_merge_raw():
    print("[INFO] Integrating raw datasets into cleaned pipeline...")
    for src in RAW_SOURCES:
        src_path = src['path']
        country = src['country']
        prefix = src['prefix']
        class_map = src['map']
        
        target_img_dir = os.path.join(CLEAN_RELABEL, country, "images")
        target_lbl_dir = os.path.join(CLEAN_RELABEL, country, "labels")
        os.makedirs(target_img_dir, exist_ok=True)
        os.makedirs(target_lbl_dir, exist_ok=True)
        
        for split in ['train', 'valid', 'test']:
            split_img_dir = os.path.join(src_path, split, "images")
            split_lbl_dir = os.path.join(src_path, split, "labels")
            
            if not os.path.exists(split_img_dir): continue
            
            print(f"  - Processing {src_path} ({split})")
            for img_file in tqdm(os.listdir(split_img_dir)):
                if not img_file.lower().endswith(('.jpg', '.jpeg', '.png')): continue
                
                base_name = os.path.splitext(img_file)[0]
                lbl_file = base_name + ".txt"
                
                src_img_path = os.path.join(split_img_dir, img_file)
                src_lbl_path = os.path.join(split_lbl_dir, lbl_file)
                
                if not os.path.exists(src_lbl_path): continue
                
                # New Filename
                new_img_name = prefix + img_file
                new_lbl_name = prefix + lbl_file
                
                # Process Label
                try:
                    with open(src_lbl_path, 'r') as f:
                        lines = f.readlines()
                except:
                    continue
                
                new_lines = []
                for line in lines:
                    parts = line.split()
                    if not parts: continue
                    try:
                        old_id = int(parts[0])
                        if old_id in class_map:
                            class_name = class_map[old_id]
                            new_id = GLOBAL_MAP[class_name]
                            parts[0] = str(new_id)
                            new_lines.append(" ".join(parts))
                    except:
                        continue
                
                if not new_lines: continue 
                
                # Copy & Write
                shutil.copy(src_img_path, os.path.join(target_img_dir, new_img_name))
                with open(os.path.join(target_lbl_dir, new_lbl_name), 'w') as f:
                    f.write("\n".join(new_lines))

def finalize_v3():
    print("[INFO] Building final v3 dataset split...")
    all_pairs = []
    
    for country in ['indonesia', 'malaysia', 'singapore', 'thailand']:
        img_dir = os.path.join(CLEAN_RELABEL, country, "images")
        lbl_dir = os.path.join(CLEAN_RELABEL, country, "labels")
        
        if not os.path.exists(img_dir): continue
        
        print(f"  - Scanning {country}...")
        for img_file in os.listdir(img_dir):
            if not img_file.lower().endswith(('.jpg', '.jpeg', '.png')): continue
            base = os.path.splitext(img_file)[0]
            lbl_file = base + ".txt"
            
            img_path = os.path.join(img_dir, img_file)
            lbl_path = os.path.join(lbl_dir, lbl_file)
            
            if os.path.exists(lbl_path):
                all_pairs.append((img_path, lbl_path, img_file, lbl_file))

    random.shuffle(all_pairs)
    
    split_idx = int(len(all_pairs) * 0.8)
    train_pairs = all_pairs[:split_idx]
    val_pairs = all_pairs[split_idx:]
    
    print(f"[SUCCESS] Total pairs found: {len(all_pairs)}")
    print(f"[SUCCESS] Training set: {len(train_pairs)}")
    print(f"[SUCCESS] Validation set: {len(val_pairs)}")
    
    for pairs, split in [(train_pairs, 'train'), (val_pairs, 'val')]:
        print(f"  - Copying {split} files...")
        for img_src, lbl_src, img_name, lbl_name in tqdm(pairs):
            shutil.copy(img_src, os.path.join(DST_V3, 'images', split, img_name))
            shutil.copy(lbl_src, os.path.join(DST_V3, 'labels', split, lbl_name))

    # Generate data.yaml
    data_yaml = {
        'path': os.path.abspath(DST_V3).replace('\\', '/'),
        'train': 'images/train',
        'val': 'images/val',
        'names': {i: name for i, name in enumerate(GLOBAL_CLASSES)}
    }
    
    with open(os.path.join(DST_V3, 'data.yaml'), 'w') as f:
        yaml.dump(data_yaml, f, sort_keys=False)
    
    print(f"[SUCCESS] v3 Dataset Ready at {DST_V3}/data.yaml")

if __name__ == "__main__":
    print("--- Starting Standardization v3 ---")
    clean_and_prepare()
    remap_and_merge_raw()
    finalize_v3()
    print("--- Completed Successfully ---")
