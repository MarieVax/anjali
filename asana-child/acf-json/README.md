# Dossier ACF JSON

Ce dossier contient les exports JSON des champs ACF (Advanced Custom Fields) pour le thème enfant.

## 📁 Utilisation

### Export des champs ACF

1. Aller dans **Custom Fields** → **Tools** dans l'admin WordPress
2. Sélectionner les groupes de champs à exporter
3. Cliquer sur **Export Field Groups**
4. Copier le contenu JSON dans un fichier `.json` dans ce dossier

### Import des champs ACF

1. Aller dans **Custom Fields** → **Tools** → **Import Field Groups**
2. Sélectionner le fichier JSON à importer
3. Cliquer sur **Import**

## 🎯 Avantages

- **Versioning** : Les champs ACF sont versionnés avec le thème
- **Déploiement** : Synchronisation automatique entre environnements
- **Collaboration** : Partage facile des configurations de champs
- **Sauvegarde** : Sécurité des configurations de champs

## 📝 Convention de nommage

Nommer les fichiers selon le format :

```
group_[nom-du-groupe]_[date].json
```

Exemple :

```
group_video_fields_2024-01-15.json
group_course_metadata_2024-01-15.json
```

## 🔧 Configuration

Le dossier `acf-json` est automatiquement reconnu par ACF Pro.
Les fichiers JSON dans ce dossier sont automatiquement importés lors de l'activation du thème.

---

**Note** : Ce dossier est essentiel pour la gestion des champs personnalisés dans le thème.

