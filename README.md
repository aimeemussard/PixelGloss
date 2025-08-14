# ✨ PixelGloss – PHP Sprite Sheet Generator

> *Where pixel precision meets Y2K glam.*

**PixelGloss** est un algorithme en PHP qui prend plusieurs images et les compile en une seule image accompagnée d’une feuille de style.
Pense-y comme au défilé de mode de tes images — parfaitement rangées, parfaitement alignées.

---
## 📸 Fonctionnalités

- 🪞 **Organisation pixel-parfaite** — arrangement optimisé des images.
- 💎 **Minimalisme élégant** — uniquement en PHP, pas encore d’interface (UI à venir).
- 🗂 **CSS mapping** — génération automatique d’une carte de coordonnées pour chaque sprite.
- 🕹 **Prêt pour le jeu et le web** — idéal pour les sprites de jeux, icônes ou animations.

---
## 🚀 Installation

### 1️⃣ Cloner le dépôt:
   ```bash
   git clone https://github.com/yourusername/PixelGloss.git
   cd pixelgloss
   ```

### 2️⃣ Vérifier que vous avez PHP **8.0+** installé.

---
## 🎮 Utilisation

1. Placez vos images sources dans un dossier à la racine du dépôt.
2. Lancez le générateur :
   ```bash
   php css_generator.php [options]
   ```
3. Votre sprite sheet et le fichier CSS seront sauvegardés à la racine du dépôt.

## ⚙️ Options

Les arguments obligatoires pour les options longues le sont aussi pour les options courtes.

Recherche les images dans le dossier passé en argument ainsi que dans tous ses sous-dossiers:

```bash
-r, --recursive
```

### Renommer l'image générée. Par défaut : sprite.png.

```bash
-i, --output-image=IMAGE
```

### Renommer la feuille de style générée. Par défaut : style.css si laissé vide.

```bash
-s, --output-style=STYLE
```

### Ajouter un espacement entre les images de NUMBER pixels.

```bash
-p, --padding=NUMBER
```
### Forcer chaque image à avoir la taille SIZExSIZE pixels.

```bash
-o, --override-size=SIZE
```

### Définir le nombre maximal d’éléments affichés horizontalement.

```bash
-c, --columns_number=NUMBER
```

---

## 📂 Exemple

**`spritesheet.png`**  
Image unique regroupant tous vos sprites. Il est possible de renommer le nom du fichier à l'aide de l'option `-i` suivi du nom du fichier ou `--output-image=IMAGE`.

**`style.css` / `index.html`**  
Fichier html et feuille de style avec les coordonnées et tailles prêtes à l’emploi, utilisable à partir des classes générées:

```bash
.image {
  background: no-repeat url('sprite.png') -0px -0px;
  width: 32px;
  height: 32px;
}
```

Il est possible de renommer le nom de la feuille de style générée à l'aide de l'option `-s` suivi du nom du fichier ou `--output-style=STYLE`.

---

## 🧪 Développement à venir

- [ ] Ajouter un drag-and-drop avec une l'interface Y2K
- [ ] Ajouter des algorithmes de placement personnalisés
- [ ] Support des sprite sheets retina/HD
- [ ] Aperçu de la palette de couleurs
- [ ] Possibilité de modifier le format de sortie de l'image (JPG, GIF)
- [ ] Création d'un fichier JSON comportant les coordonnées.

---

## 💖 Credits

Fait avec ✨ des pixels et du glamour ✨ par **Aimée Mussard**.

---

## 💻 Bon développement !

N'hésitez pas à me contacter si vous avez des questions ou des retours.
