import os
import glob
from PIL import Image, ImageChops

WORKSPACE_DIR = os.path.dirname(os.path.abspath(__file__))
IMAGES_DIR = os.path.join(WORKSPACE_DIR, 'assets', 'images')
BACKUP_DIR = os.path.join(WORKSPACE_DIR, 'assets', 'images_backup')

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

PORTRAIT_VEHICLES = {
    'benz_real.jpg': (0, 220, 800, 780),
    'bmw_real.jpg': (0, 180, 800, 720),
    'compact_suv_real.jpg': (0, 180, 800, 720),
    'suv_real.jpg': (0, 200, 800, 780),
}

def make_transparent_cutout(img_path, filename):
    img = Image.open(img_path)
    
    # If portrait vehicle image, crop landscape region around car body first
    if filename in PORTRAIT_VEHICLES:
        crop_box = PORTRAIT_VEHICLES[filename]
        img = img.crop(crop_box)

    # 1. Existing RGBA cutouts (car1.png, car2.png, car3.png, car-bg.png)
    if img.mode == 'RGBA':
        alpha = img.split()[3]
        bbox = alpha.getbbox()
        if bbox:
            pad = 10
            x0 = max(0, bbox[0] - pad)
            y0 = max(0, bbox[1] - pad)
            x1 = min(img.width, bbox[2] + pad)
            y1 = min(img.height, bbox[3] + pad)
            cropped = img.crop((x0, y0, x1, y1))
        else:
            cropped = img
        return cropped

    # 2. Studio backdrops -> Convert to high quality transparent PNG cutouts
    img_rgba = img.convert('RGBA')
    width, height = img_rgba.size
    
    # Get corner pixel samples for background color
    corners = [
        img_rgba.getpixel((0, 0)),
        img_rgba.getpixel((width - 1, 0)),
        img_rgba.getpixel((0, height - 1)),
        img_rgba.getpixel((width - 1, height - 1)),
        img_rgba.getpixel((width // 2, 0)),
        img_rgba.getpixel((width // 2, height - 1))
    ]
    
    bg_r = sum(c[0] for c in corners) / len(corners)
    bg_g = sum(c[1] for c in corners) / len(corners)
    bg_b = sum(c[2] for c in corners) / len(corners)
    avg_bg = (bg_r + bg_g + bg_b) / 3
    
    if avg_bg > 130:
        # Transparent keying for studio backgrounds
        tolerance = 28
        newData = []
        pixels = list(img_rgba.getdata())
        for item in pixels:
            r, g, b, a = item
            diff = ((r - bg_r)**2 + (g - bg_g)**2 + (b - bg_b)**2)**0.5
            brightness = (r + g + b) / 3
            
            if diff < tolerance or (brightness > 238 and diff < tolerance * 2.2):
                newData.append((255, 255, 255, 0))
            elif diff < tolerance * 2.2:
                alpha_val = int(255 * ((diff - tolerance) / (tolerance * 1.2)))
                alpha_val = max(0, min(255, alpha_val))
                newData.append((r, g, b, alpha_val))
            else:
                newData.append((r, g, b, 255))
        img_rgba.putdata(newData)

    # Crop tightly around car silhouette
    alpha = img_rgba.split()[3]
    bbox = alpha.getbbox()
    
    if not bbox:
        # Fallback difference mask
        bg_img = Image.new('RGB', (width, height), (int(bg_r), int(bg_g), int(bg_b)))
        diff = ImageChops.difference(img.convert('RGB'), bg_img).convert('L')
        mask = diff.point(lambda p: 255 if p > 20 else 0)
        bbox = mask.getbbox()

    if bbox:
        pad = 10
        x0 = max(0, bbox[0] - pad)
        y0 = max(0, bbox[1] - pad)
        x1 = min(width, bbox[2] + pad)
        y1 = min(height, bbox[3] + pad)
        cropped = img_rgba.crop((x0, y0, x1, y1))
    else:
        cropped = img_rgba
        
    return cropped

def main():
    print("=== Processing High-Quality Vehicle Cutouts ===")
    count = 0
    for filename in VEHICLE_IMAGES:
        src = os.path.join(BACKUP_DIR, filename)
        dst = os.path.join(IMAGES_DIR, filename)
        
        if not os.path.exists(src):
            print(f"File missing in backup: {filename}")
            continue
            
        result_img = make_transparent_cutout(src, filename)
        
        # Save as clean high-res PNG for drop-shadow alpha support
        result_img.save(dst, format='PNG', compress_level=1)
        print(f"[OK] {filename}: final cutout size {result_img.size}")
        count += 1
        
    print(f"\nDone processing {count} vehicles!")

if __name__ == '__main__':
    main()
