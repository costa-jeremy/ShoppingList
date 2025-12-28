const fs = require('fs');

// Créer un PNG simple avec Canvas (si disponible) ou utiliser une approche alternative
// Pour l'instant, créons des fichiers PNG basiques qui seront remplacés manuellement si nécessaire

const svgContent = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
  <circle cx="50" cy="50" r="50" fill="#198754"/>
  <text x="50" y="70" font-size="60" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-weight="bold">C</text>
</svg>`;

// Créer un fichier HTML pour conversion manuelle
const htmlContent = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Favicon Generator</title>
</head>
<body>
    <h1>Générateur de favicons</h1>
    <p>Ouvrez cette page dans votre navigateur et utilisez la console pour télécharger les images.</p>
    
    <canvas id="canvas192" width="192" height="192" style="border:1px solid black"></canvas>
    <canvas id="canvas512" width="512" height="512" style="border:1px solid black"></canvas>
    
    <script>
        const svgContent = \`${svgContent}\`;
        
        function drawSVG(canvasId, size) {
            const canvas = document.getElementById(canvasId);
            const ctx = canvas.getContext('2d');
            const img = new Image();
            const svg = new Blob([svgContent], {type: 'image/svg+xml'});
            const url = URL.createObjectURL(svg);
            
            img.onload = function() {
                ctx.drawImage(img, 0, 0, size, size);
                URL.revokeObjectURL(url);
                
                // Télécharger automatiquement
                canvas.toBlob(function(blob) {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = 'favicon-' + size + '.png';
                    a.click();
                });
            };
            
            img.src = url;
        }
        
        setTimeout(() => drawSVG('canvas192', 192), 100);
        setTimeout(() => drawSVG('canvas512', 512), 500);
    </script>
</body>
</html>`;

fs.writeFileSync('/home/jeremy/Docker/apps/projectshoppinglist/public/generate-favicon.html', htmlContent);
console.log('Fichier HTML généré : public/generate-favicon.html');
console.log('Ouvrez ce fichier dans votre navigateur pour générer les PNG.');

