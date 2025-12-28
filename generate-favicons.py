#!/usr/bin/env python3
import subprocess
import sys

# Vérifier si cairosvg est installé
try:
    import cairosvg
    use_cairosvg = True
except ImportError:
    use_cairosvg = False
    print("cairosvg n'est pas installé. Tentative d'installation...")
    try:
        subprocess.check_call([sys.executable, "-m", "pip", "install", "--user", "cairosvg"])
        import cairosvg
        use_cairosvg = True
        print("cairosvg installé avec succès!")
    except Exception as e:
        print(f"Impossible d'installer cairosvg: {e}")
        print("Alternative: utilisation d'Inkscape...")
        use_cairosvg = False

svg_file = '/home/jeremy/Docker/apps/projectshoppinglist/public/favicon.svg'
output_192 = '/home/jeremy/Docker/apps/projectshoppinglist/public/favicon-192.png'
output_512 = '/home/jeremy/Docker/apps/projectshoppinglist/public/favicon-512.png'

if use_cairosvg:
    # Utiliser cairosvg
    cairosvg.svg2png(url=svg_file, write_to=output_192, output_width=192, output_height=192)
    cairosvg.svg2png(url=svg_file, write_to=output_512, output_width=512, output_height=512)
    print(f"PNG générés avec succès:")
    print(f"  - {output_192}")
    print(f"  - {output_512}")
else:
    # Essayer avec Inkscape
    try:
        subprocess.run(['inkscape', '--version'], check=True, capture_output=True)
        subprocess.run(['inkscape', svg_file, '-w', '192', '-h', '192', '-o', output_192], check=True)
        subprocess.run(['inkscape', svg_file, '-w', '512', '-h', '512', '-o', output_512], check=True)
        print(f"PNG générés avec succès avec Inkscape:")
        print(f"  - {output_192}")
        print(f"  - {output_512}")
    except Exception as e:
        print(f"Erreur: {e}")
        print("\nVeuillez générer manuellement les fichiers PNG:")
        print(f"1. Ouvrez {svg_file} dans un éditeur d'images (GIMP, Inkscape, etc.)")
        print(f"2. Exportez en PNG aux tailles 192x192 et 512x512")
        print(f"3. Sauvegardez comme favicon-192.png et favicon-512.png dans public/")

