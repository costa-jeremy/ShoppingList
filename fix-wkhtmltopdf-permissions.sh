#!/bin/bash
# Script pour corriger les permissions de wkhtmltopdf après une installation/mise à jour de composer

echo "🔧 Correction des permissions pour wkhtmltopdf..."

WKHTMLTOPDF_PATH="vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64"

if [ -f "$WKHTMLTOPDF_PATH" ]; then
    chmod +x "$WKHTMLTOPDF_PATH"
    echo "✅ Permissions corrigées pour $WKHTMLTOPDF_PATH"
else
    echo "❌ Fichier $WKHTMLTOPDF_PATH introuvable"
    exit 1
fi

echo "✅ Terminé !"

