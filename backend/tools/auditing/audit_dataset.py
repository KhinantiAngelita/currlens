import os
import yaml
from collections import Counter

def audit_dataset():
    # 1. Read class names from multi_currency.yml
    config_path = os.path.join('training', 'multi_currency.yml')
    with open(config_path, 'r') as f:
        config = yaml.safe_load(f)
    
    class_names = config.get('names', {})
    
    # 2. Setup counters
    counts = Counter()
    
    # 3. Walk through dataset_clean_relabel
    root_dir = os.path.join('data', 'dataset_clean_relabel')
    countries = ['indonesia', 'malaysia', 'singapore', 'thailand']
    
    for country in countries:
        label_dir = os.path.join(root_dir, country, 'labels')
        if not os.path.exists(label_dir):
            continue
            
        for file in os.listdir(label_dir):
            if file.endswith('.txt'):
                with open(os.path.join(label_dir, file), 'r') as f:
                    for line in f:
                        parts = line.split()
                        if parts:
                            class_id = int(parts[0])
                            counts[class_id] += 1
                            
    # 4. Print results
    print("-" * 50)
    print(f"{'Class ID':<10} {'Class Name':<20} {'Count':<10}")
    print("-" * 50)
    
    for class_id in sorted(class_names.keys()):
        name = class_names[class_id]
        count = counts.get(class_id, 0)
        print(f"{class_id:<10} {name:<20} {count:<10}")
    print("-" * 50)

if __name__ == "__main__":
    audit_dataset()
