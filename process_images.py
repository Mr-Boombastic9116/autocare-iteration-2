import os
import glob
import shutil
from PIL import Image, ImageChops

WORKSPACE_DIR = os.path.dirname(os.path.abspath(__file__))
IMAGES_DIR = os.path.join(WORKSPACE_DIR, 'assets', 'images')
BACKUP_DIR = os.path.join(WORKSPACE_DIR, 'assets', 'images_backup')

TARGET_SIZE = (800, 533)  # Standard 3:2 landscape canvas frame

VEHICLE_IMAGES = [
    'benz_real.jpg',
    'bike_real.jpg',
    'bmw_real.jpg',
    'car-bg.png',
    'car1.png',
    'car2.png',
    'car3.png',
    'city.png',
    'compact_suv_real.jpg',
    'creta.png',
    'ev_real.jpg',
    'fortuner.png',
    'hatchback_real.jpg',
    'luxury_suv_real.jpg',
    'nexon.png',
    'pickup_real.jpg',
    'porsche_real.jpg',
    'sedan_real.jpg',
    'supercar_real.jpg',
    'suv_real.jpg',
    'swift.png',
    'thar.png'
]

def restore_non_vehicles():
    """Restore non-vehicle assets (logos, icons, maps, etc.) from backup."""
    if not os.path.exists(BACKUP_DIR):
        print("Backup dir does not exist.")
        return
        
    for fname in os.listdir(BACKUP_DIR):
        if fname not in VEHICLE_IMAGES:
            src = os.path.join(BACKUP_DIR, fname)
            dst = os.path.join(IMAGES_DIR, fname)
            if os.path.isfile(src):
                shutil.copy2(src, dst)
                print(f"Restored original UI asset: {fname}")

def process_vehicle_image(filename):
    file_path = os.path.join(IMAGES_DIR, filename)
    backup_path = os.path.join(BACKUP_DIR, filename)
    
    # Read from backup to get clean original
    read_path = backup_path if os.path.exists(backup_path) else file_path
    
    if not os.path.exists(read_path) or os.path.getsize(read_path) < 100:
        print(f"Skipping missing or invalid file: {filename}")
        return False
        
    try:
        img = Image.open(read_path)
    except Exception as e:
        print(f"Error opening {filename}: {e}")
        return False

    if img.mode == 'RGBA':
        alpha = img.split()[3]
        bbox = alpha.getbbox()
        cropped = img.crop(bbox) if bbox else img

        padding_pct = 0.04
        max_w = int(TARGET_SIZE[0] * (1 - 2 * padding_pct))
        max_h = int(TARGET_SIZE[1] * (1 - 2 * padding_pct))

        w, h = cropped.size
        scale = min(max_w / max(1, w), max_h / max(1, h))
        new_w = max(1, int(w * scale))
        new_h = max(1, int(h * scale))

        resized = cropped.resize((new_w, new_h), Image.Resampling.LANCZOS)

        canvas = Image.new('RGB', TARGET_SIZE, (255, 255, 255))
        paste_x = (TARGET_SIZE[0] - new_w) // 2
        paste_y = (TARGET_SIZE[1] - new_h) // 2
        canvas.paste(resized, (paste_x, paste_y), resized)

        canvas.save(file_path, format='PNG', quality=95)
        print(f"[SUCCESS] RGBA Vehicle {filename}: cropped {w}x{h} -> resized {new_w}x{new_h} into {TARGET_SIZE} (white bkg)")
        return True

    # RGB images
    img_rgb = img.convert('RGB')
    
    # Sample corner background color
    corners = [
        img_rgb.getpixel((0, 0)),
        img_rgb.getpixel((img_rgb.width - 1, 0)),
        img_rgb.getpixel((0, img_rgb.height - 1)),
        img_rgb.getpixel((img_rgb.width - 1, img_rgb.height - 1)),
        img_rgb.getpixel((img_rgb.width // 2, 0)),
        img_rgb.getpixel((img_rgb.width // 2, img_rgb.height - 1))
    ]
    bg_color = max(set(corners), key=corners.count)

    bg = Image.new('RGB', img_rgb.size, bg_color)
    diff = ImageChops.difference(img_rgb, bg)
    diff_l = diff.convert('L')

    threshold = 18
    mask = diff_l.point(lambda p: 255 if p > threshold else 0)
    bbox = mask.getbbox()

    if bbox:
        pad = 6
        x0 = max(0, bbox[0] - pad)
        y0 = max(0, bbox[1] - pad)
        x1 = min(img_rgb.width, bbox[2] + pad)
        y1 = min(img_rgb.height, bbox[3] + pad)
        cropped = img_rgb.crop((x0, y0, x1, y1))
    else:
        cropped = img_rgb

    w, h = cropped.size
    padding_pct = 0.04
    max_w = int(TARGET_SIZE[0] * (1 - 2 * padding_pct))
    max_h = int(TARGET_SIZE[1] * (1 - 2 * padding_pct))

    scale = min(max_w / max(1, w), max_h / max(1, h))
    new_w = max(1, int(w * scale))
    new_h = max(1, int(h * scale))

    resized = cropped.resize((new_w, new_h), Image.Resampling.LANCZOS)

    canvas = Image.new('RGB', TARGET_SIZE, (255, 255, 255))
    paste_x = (TARGET_SIZE[0] - new_w) // 2
    paste_y = (TARGET_SIZE[1] - new_h) // 2
    canvas.paste(resized, (paste_x, paste_y))

    ext = os.path.splitext(filename)[1].lower()
    fmt = 'PNG' if ext == '.png' else 'JPEG'
    canvas.save(file_path, format=fmt, quality=95)

    print(f"[SUCCESS] RGB Vehicle {filename}: cropped {w}x{h} -> resized {new_w}x{new_h} into {TARGET_SIZE} (white bkg)")
    return True

def main():
    print("--- Restoring Non-Vehicle UI Assets ---")
    restore_non_vehicles()
    
    print("\n--- Processing Vehicle Images ---")
    processed_count = 0
    for v_img in VEHICLE_IMAGES:
        if process_vehicle_image(v_img):
            processed_count += 1
            
    print(f"\nSuccessfully processed {processed_count} vehicle images.")

if __name__ == '__main__':
    main()
