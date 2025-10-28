Objectif : Comprendre et implémenter le moteur de template principal de Laravel, Blade, en utilisant des layouts (mises en page) et des composants dynamiques pour créer une structure réutilisable pour les trois pages principales de la boutique.

1. Mise en place des Routes Principales (3 Pages)

Nous allons définir trois routes pour les pages principales de notre boutique : Accueil (pour la sélection principale des produits), Produits (Products) et Contact.

## A. Définition des Routes

Assurez-vous que votre fichier routes/web.php contienne les trois routes suivantes.

```PHP
// routes/web.php

Route::get('/', function () {
    return view('home');
});

Route::get('/products', function () {
    return view('products');
});

Route::get('/contact', function () {
    return view('contact');
});
```

## B. Création et Remplissage des Vues

1. **Renommer la Vue par Défaut** : Renommez `resources/views/welcome.blade.php` en `resources/views/home.blade.php`.
2. **Créer les Nouvelles Vues** : Créez deux nouveaux fichiers : `resources/views/products.blade.php` et `resources/views/contact.blade.php`.
3. **Ajouter le Contenu Initial** : Pour l'instant, copiez le HTML de base du tableau ci-dessous dans les nouveaux fichiers respectifs, à l'intérieur d'une balise `<h1>`.

| Page (.blade.php) | Texte de l'en-tête                                  |
|-------------------|-----------------------------------------------------|
| `home`            | `<h1>Bienvenue à la Boutique de Produits Frais !</h1>` |
| `products`        | `<h1>Notre Histoire : de la Ferme à la Table.</h1>`   |
| `contact`         | `<h1>Contactez Notre Équipe.</h1>`                    |

Maintenant, essayez de changer l'URI de votre navigateur en /products par exemple.

## C. Créer notre barre de navigation

Maintenant que nos pages fonctionnent, nous avons besoin d'un moyen de naviguer facilement entre elles. Créons un élément `<nav>`. Ajoutez ceci à notre fichier home.blade.php :

```HTML
<nav>
 <a href="/">Home</a>
 <a href="/products">Products</a>
 <a href="/contact">Contact</a>
</nav>
```

### 2. Introduction à Blade pour les Layouts et Composants

Ajouter manuellement une barre de navigation à trois fichiers, c'est acceptable, mais imaginez 50 pages ! Nous avons besoin d'un Layout (mise en page) réutilisable.

## A. Le Moteur de Template Blade

Blade est le puissant moteur de template de Laravel qui fournit une syntaxe simple pour les tâches courantes comme la définition de layouts, l'utilisation de boucles et l'affichage de données.

## B. Création du Composant de Layout de Base

Au lieu des fichiers de layout traditionnels, Laravel 11 encourage l'utilisation des Composants de Vue (View Components).

1. **Créer le Répertoire des Composants** : Créez un nouveau répertoire : `resources/views/components`.
2. **Créer le Fichier de Layout** : À l'intérieur de ce répertoire, créez le fichier `resources/views/components/layout.blade.php`.
3. **Déplacer le HTML Commun** : Déplacez le HTML commun (par exemple, `<html>`, `<head>`, la structure de base `<body>`, et la barre de navigation) de votre fichier `home.blade.php` vers ce nouveau fichier `layout.blade.php`.

## C. Définir le Point d'Injection de Contenu

Dans components/layout.blade.php, nous devons définir où ira le contenu spécifique à la page.

```HTML
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body>
        <nav>
            <a href="/">Home</a>
            <a href="/products">Products</a>
            <a href="/contact">Contact</a>
        </nav>
        <div class="page-content"> {{ $slot }} </div>
    </body>
</html>
```

> [!NOTE]
> **Comprendre `{{ $slot }}`**
>
> * **Raccourci de Syntaxe :** La syntaxe `{{ $slot }}` est une manière concise et propre d'écrire `<?php echo $slot; ?>` en PHP standard.
> * **Sécurité Automatique :** Blade échappe automatiquement toutes les données affichées avec les doubles accolades `{{ }}` pour protéger votre application contre les attaques XSS (Cross-Site Scripting).

## D. Utiliser le Layout dans les Vues

Maintenant, nettoyez vos trois vues principales (home.blade.php, products.blade.php, contact.blade.php). Supprimez tout le HTML d'enrobage et référencez simplement le composant de layout en utilisant la balise `<x-layout>`.

```HTML
{{-- /views/home.blade.php --}}
<x-layout>
    <h1 class="text-3xl font-bold">Bienvenue à la Boutique de Produits Frais !</h1>
    <p>Parcourez nos sélections de saison.</p>
</x-layout>
```

Faites de même pour les deux autres.

## 3. Création d'un Composant de Lien de Navigation Dynamique

Pour rendre nos liens de navigation réutilisables et faciles à styliser globalement, transformons-les en composant.

### A. Création du Composant NavLink

1. **Créer le Fichier du Composant**
        Créez un nouveau fichier à l'emplacement `resources/views/components/nav-link.blade.php`.

2. **Ajouter le Balisage de Base**
        Ajoutez le code suivant au fichier `nav-link.blade.php`. Ce balisage utilise deux variables spéciales de Blade : `$attributes` et `$slot`.

```html
        <a {{ $attributes }}>{{ $slot }}</a>
```

> [!NOTE]
        > **Comprendre `$attributes` et `$slot`**
        >
        > ***`$attributes`** : Cette variable spéciale collecte tous les attributs HTML (comme `href`, `class`, `style`, etc.) qui sont passés à la balise du composant. Blade les fusionne automatiquement sur l'élément `<a>`.
        >*   **`$slot`** : Cette variable contient tout le contenu placé entre les balises d'ouverture et de fermeture du composant. Par exemple, pour `<x-nav-link href="/">Accueil</x-nav-link>`, la valeur de `$slot` serait "Accueil".

## B. Utiliser le Composant NavLink

Mettez à jour la section de navigation dans votre resources/views/components/layout.blade.php:

```HTML
<nav>
<x-nav-link href="/">Accueil</x-nav-link>
<x-nav-link href="/products">Produits</x-nav-link>
<x-nav-link href="/contact">Contact</x-nav-link>
<x-nav-link href="/seasonal" class="text-green-600">Produits de Saison</x-nav-link>
</nav>
```

Actualisez votre navigateur. Les liens devraient maintenant naviguer correctement et accepter des attributs comme class ou style.

### 4. Appliquer des Styles au Layout de la Boutique avec Tailwind CSS

Pour donner à notre boutique une apparence professionnelle, nous allons rapidement ajouter des styles de base avec Tailwind CSS.

## A. Configuration Rapide via CDN

Comme nous nous concentrons sur Blade, nous utiliserons le CDN pour les styles afin d'éviter les étapes de build frontend pour le moment.

Ajoutez la balise `<script>` suivante dans le `<head>` de votre resources/views/components/layout.blade.php`:

```HTML

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
            <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="h-full font-sans">
        <div class="min-h-full">
            <nav class="bg-green-700">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <div class="flex items-center">
                            <div class="shrink-0">
                                <a href="/" class="flex items-center text-white text-xl font-bold">
                                    <span class="text-3xl mr-2">🥕</span> Le Panier de la Récolte
                                </a>
                            </div>
                            <div class="hidden md:block">
                                <div class="ml-10 flex items-baseline space-x-4">
                                    <x-nav-link href="/">Accueil</x-nav-link>
                                    <x-nav-link href="/products">Produits</x-nav-link>
                                    <x-nav-link href="/about">À Propos</x-nav-link>
                                    <x-nav-link href="/contact">Contact</x-nav-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
```

## B. Implémenter un Slot d'En-tête

Les vrais layouts ont besoin d'un titre unique pour la zone de contenu principal de chaque page.

Définir un Slot Nommé dans le Layout : Mettez à jour components/layout.blade.php pour inclure un emplacement pour un en-tête dynamique.

```HTML

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900"> {{ $heading }} </h1>
    </div>
</header>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8"> {{ $slot }} </div>
</main>

Passer des Données au Slot Nommé : Dans vos vues, passez l'en-tête en utilisant une balise spéciale <x-slot>.
HTML

    {{--/views/home.blade.php--}}
    <x-layout>
     <x-slot name="heading" class="text-3xl font-bold">Accueil : Produits Frais de Saison</x-slot>
     <p>Nos fruits et légumes en vedette cette semaine...</p>
     </x-layout>
```

Maintenant, essayez de naviguer vers /products ou /contact et voyez ce qui se passe !

> [!WARNING]
> **Erreur à venir : Variable non définie**
>
> Puisque nous avons ajouté le slot nommé `$heading` à notre `layout.blade.php`, toute vue qui utilise `<x-layout>` est maintenant *obligée* de lui fournir du contenu.
>
> Comme `products.blade.php` et `contact.blade.php` ne définissent pas encore ce slot, visiter `/products` ou `/contact` déclenchera désormais une erreur "Undefined variable: `heading`". C'est normal ! Nous allons corriger cela ensuite.

## C. Rendre notre en-tête dynamique

Blade nous offre plusieurs fonctions utiles, elles commencent toutes par le symbole @. Utilisons @isset, qui affiche une section en fonction d'une variable donnée. Si cette dernière existe, il affichera la section, sinon il n'affichera pas du tout la section qu'il encapsule. Encapsulez toute la section `<header>` comme ceci.

```HTML
            @isset($header)
                <header class="bg-white shadow">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
```
