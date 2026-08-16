import os
import glob
import urllib.request
from PIL import Image, ImageChops

WORKSPACE_DIR = os.path.dirname(os.path.abspath(__file__))
IMAGES_DIR = os.path.join(WORKSPACE_DIR, 'assets', 'images')
BACKUP_DIR = os.path.join(WORKSPACE_DIR, 'assets', 'images_backup')

# Standard uniform frame size for all vehicle images
TARGET_SIZE = (800, 533)  # 3:2 landscape ratio

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

# Custom crop windows for real photo assets to tightly isolate the vehicle
CUSTOM_CROP_BOXES = {
    'bmw_real.jpg': None,  # Handled via downloaded new BMW image
    'benz_real.jpg': (0, 220, 800, 780),
    'compact_suv_real.jpg': (0, 180, 800, 720),
    'suv_real.jpg': (0, 200, 800, 780),
}

def download_new_bmw():
    """Download a new real BMW car image from Wikimedia Commons."""
    url = 'https://upload.wikimedia.org/wikipedia/commons/3/31/2018_BMW_M5_Automatic_4.4.jpg'
    target_path = os.path.join(BACKUP_DIR, 'bmw_real.jpg')
    print("Downloading new BMW image from web...")
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req) as resp, open(target_path, 'wb') as f:
            f.write(resp.read())
        print(f"Downloaded new BMW image to {target_path}")
        return True
    except Exception as e:
        print(f"Failed to download BMW image: {e}")
        return False

def make_uniform_white_bg_image(filename):
    file_path = os.path.join(IMAGES_DIR, filename)
    backup_path = os.path.join(BACKUP_DIR, filename)
    
    read_path = backup_path if os.path.exists(backup_path) else file_path
    if not os.path.exists(read_path):
        print(f"File not found: {filename}")
        return False

    img = Image.open(read_path)

    # 1. Custom crop window for real vehicle photos if specified
    if filename in CUSTOM_CROP_BOXES and CUSTOM_CROP_BOXES[filename]:
        img = img.crop(CUSTOM_CROP_BOXES[filename])

    # 2. Extract subject cutout
    if img.mode == 'RGBA':
        alpha = img.split()[3]
        bbox = alpha.getbbox()
        cropped = img.crop(bbox) if bbox else img
        
        # Scale subject to fit ~88% of target canvas width/height
        max_w = int(TARGET_SIZE[0] * 0.88)
        max_h = int(TARGET_SIZE[1] * 0.88)
        
        w, h = cropped.size
        scale = min(max_w / max(1, w), max_h / max(1, h))
        new_w = max(1, int(w * scale))
        new_h = max(1, int(h * scale))
        
        resized = cropped.resize((new_w, new_h), Image.Resampling.LANCZOS)
        
        # Create solid white canvas
        canvas = Image.new('RGB', TARGET_SIZE, (255, 255, 255))
        paste_x = (TARGET_SIZE[0] - new_w) // 2
        paste_y = (TARGET_SIZE[1] - new_h) // 2
        
        # Composite RGBA image over solid white canvas
        canvas.paste(resized, (paste_x, paste_y), resized)
        
        ext = os.path.splitext(filename)[1].lower()
        if ext == '.png':
            canvas.save(file_path, format='PNG', quality=95)
        else:
            canvas.save(file_path, format='JPEG', quality=95)
        print(f"[UNIFORM WHITE] RGBA {filename} -> {TARGET_SIZE} (white bkg)")
        return True

    # RGB images
    img_rgb = img.convert('RGB')
    width, height = img_rgb.size
    
    # Check if background is studio / light color
    corners = [
        img_rgb.getpixel((0, 0)),
        img_rgb.getpixel((width - 1, 0)),
        img_rgb.getpixel((0, height - 1)),
        img_rgb.getpixel((width - 1, height - 1)),
        img_rgb.getpixel((width // 2, 0)),
        img_rgb.getpixel((width // 2, height - 1))
    ]
    bg_r = sum(c[0] for c in corners) / len(corners)
    bg_g = sum(c[1] for c in corners) / len(corners)
    bg_b = sum(c[2] for c in corners) / len(corners)
    avg_bg = (bg_r + bg_g + bg_b) / 3

    # If studio light background, remove background to make pure white
    if avg_bg > 120:
        img_rgba = img_rgb.convert('RGBA')
        datas = list(img_rgba.getdata())
        newData = []
        tolerance = 32
        for item in datas:
            r, g, b, a = item
            diff = ((r - bg_r)**2 + (g - bg_g)**2 + (b - bg_b)**2)**0.5
            brightness = (r + g + b) / 3
            if diff < tolerance or (brightness > 235 and diff < tolerance * 2.2):
                newData.append((255, 255, 255, 0))
            elif diff < tolerance * 2.2:
                alpha_val = int(255 * ((diff - tolerance) / (tolerance * 1.2)))
                newData.append((r, g, b, max(0, min(255, alpha_val))))
            else:
                newData.append((r, g, b, 255))
        img_rgba.putdata(newData)
        
        alpha = img_rgba.split()[3]
        bbox = alpha.getbbox()
        cropped = img_rgba.crop(bbox) if bbox else img_rgba
        
        max_w = int(TARGET_SIZE[0] * 0.88)
        max_h = int(TARGET_SIZE[1] * 0.88)
        w, h = cropped.size
        scale = min(max_w / max(1, w), max_h / max(1, h))
        new_w = max(1, int(w * scale))
        new_h = max(1, int(h * scale))
        
        resized = cropped.resize((new_w, new_h), Image.Resampling.LANCZOS)
        canvas = Image.new('RGB', TARGET_SIZE, (255, 255, 255))
        paste_x = (TARGET_SIZE[0] - new_w) // 2
        paste_y = (TARGET_SIZE[1] - new_h) // 2
        canvas.paste(resized, (paste_x, paste_y), resized)
    else:
        # Non-studio background: Difference crop and place on white canvas
        bg_img = Image.new('RGB', (width, height), (int(bg_r), int(bg_g), int(bg_b)))
        diff = ImageChops.difference(img_rgb, bg_img).convert('L')
        mask = diff.point(lambda p: 255 if p > 20 else 0)
        bbox = mask.getbbox()
        cropped = img_rgb.crop(bbox) if bbox else img_rgb
        
        max_w = int(TARGET_SIZE[0] * 0.88)
        max_h = int(TARGET_SIZE[1] * 0.88)
        w, h = cropped.size
        scale = min(max_w / max(1, w), max_h / max(1, h))
        new_w = max(1, int(w * scale))
        new_h = max(1, int(h * scale))
        
        resized = cropped.resize((new_w, new_h), Image.Resampling.LANCZOS)
        canvas = Image.new('RGB', TARGET_SIZE, (255, 255, 255))
        paste_x = (TARGET_SIZE[0] - new_w) // 2
        paste_y = (TARGET_SIZE[1] - new_h) // 2
        canvas.paste(resized, (paste_x, paste_y))

    ext = os.path.splitext(filename)[1].lower()
    if ext == '.png':
        canvas.save(file_path, format='PNG', quality=95)
    else:
        canvas.save(file_path, format='JPEG', quality=95)
        
    print(f"[UNIFORM WHITE] RGB {filename} -> {TARGET_SIZE} (white bkg)")
    return True

def main():
    print("=== Step 1: Downloading New BMW Image ===")
    download_new_bmw()
    
    print("\n=== Step 2: Processing All Vehicles to Uniform White Background (800x533) ===")
    count = 0
    for fname in VEHICLE_IMAGES:
        if make_uniform_white_bg_image(fname):
            count += 1
            
    print(f"\nSuccessfully created {count} uniform white background vehicle images.")

if __name__ == '__main__':
    main()
