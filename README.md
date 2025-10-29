**Objectif :** Implémenter un style "actif" dynamique sur la barre de navigation à l’aide de composants Blade et apprendre à passer des données (comme une liste de produits) d’une route à une vue pour l’affichage.

---

## 1. Mise en place du style actif sur la navigation

Actuellement, les liens de navigation n’indiquent pas visuellement la page courante. Nous allons corriger cela avec un **style conditionnel**.

### A. Mise en place d’un conteneur pleine hauteur

Pour que la mise en page de votre boutique occupe toute la hauteur de la fenêtre et ait un fond cohérent, mettez à jour les balises `<html>` et `<body>` dans votre fichier de layout de base (`resources/views/components/layout.blade.php`).

```html
<!DOCTYPE html>
<html lang="fr" class="h-full bg-gray-50">
 <head>
    </head>
 <body class="h-full font-sans"> </body>
</html>
```

### B. Logique de style conditionnel (Le helper Request)

Nous utilisons le helper `request()` de Laravel avec la méthode `is()` pour vérifier l’URI courante.

- **Classes actives (page courante) :** `bg-green-700 text-white` (Spécifique à la boutique : fond vert foncé, texte blanc)

- **Classes inactives :** `text-gray-300 hover:bg-green-600 hover:text-white` (Texte plus clair, survol vert)

**Exemple de logique pour le lien Accueil (avant refactorisation en composant) :**

Remplacez vos liens par ceci

```html
{{--/views/layout.blade.php--}}
<x-nav-link  href="/" class="{{ request()->is('/') ? 'bg-green-700 text-white rounded-md px-3 py-2 text-sm font-medium' : 'text-gray-300 hover:bg-green-600 hover:text-white rounded-md px-3 py-2 text-sm font-medium' }}"> Accueil </x-nav-link>
```

### C. Refactorisation dans le composant `<x-nav-link>`

- **Déclarer une propriété :** Ouvrez `resources/views/components/nav-link.blade.php`. Nous utiliserons la directive `@props` pour indiquer au composant qu’il attend un attribut `active`, qui sera `false` par défaut.

- **Ajouter des classes conditionnelles :** Nous utiliserons un opérateur ternaire pour appliquer différentes classes CSS selon que `$active` est vrai ou faux.

```html

@props(['active' => false]) <a {{ $attributes->merge(['class' => $active
    ? 'bg-green-700 text-white rounded-md px-3 py-2 text-sm font-medium'
    : 'text-gray-300 hover:bg-green-600 hover:text-white rounded-md px-3 py-2 text-sm font-medium'])
}}>
    {{ $slot }}
</a>
```

- **Note sur `$attributes->merge()` :** Cette méthode combine intelligemment les classes définies ici avec toute classe personnalisée passée lors de l’utilisation du composant.

- **Utilisation du composant dans le layout :** Mettez à jour votre navigation dans `resources/views/components/layout.blade.php`.

```html
 {{-- resources/views/components/layout.blade.php --}}
 <div class="ml-10 flex items-baseline space-x-4"> <x-nav-link href="/" :active="request()->is('/')">Accueil</x-nav-link>
 <x-nav-link href="/products" :active="request()->is('products')">Produits</x-nav-link>
 <x-nav-link href="/contact" :active="request()->is('contact')">Contact</x-nav-link> </div>
```

>[!Critical]
>
 **La syntaxe avec deux-points (`:`)** Remarquez le deux-points (`:active`). Cela indique à Blade d’évaluer la valeur comme une **expression PHP** (qui retourne `true` ou `false`). Sans le deux-points, Blade traiterait `"request()->is('/')"` comme une simple chaîne, qui serait toujours "vraie", rendant le lien toujours actif.

---

## 2. Passage de données des routes vers les vues

Une boutique statique n’est pas utile. Nous devons récupérer et afficher dynamiquement des listes de produits.

### A. Passage simple de données (Route vers Vue)

Nous utilisons le second argument de la fonction `view()`, un tableau, pour passer des données.

**Mettez à jour `routes/web.php` :**

```php
// routes/web.php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'season' => 'Automne',
        'shop_name' => 'Le Panier de la Récolte',
    ]);
});
// ... autres routes

// Exemple de route pour la liste des produits disponibles
Route::get('/products', function () {
    return view('products', ['heading' => 'Nos produits frais', 'produce' => [['id' => 1, 'name' => 'Patate douce', 'price' => 2.99, 'in_stock' => true], ['id' => 2, 'name' => 'Pomme Granny Smith', 'price' => 1.50, 'in_stock' => true], ['id' => 3, 'name' => 'Bouquet d’herbes fraîches', 'price' => 4.50, 'in_stock' => false],]]);
});
```

### B. Accès aux données dans la vue

Les clés du tableau deviennent des variables directes dans vos vues Blade.

**Exemple dans `resources/views/home.blade.php` :**

```html
<x-layout>
 <x-slot name="header">Bienvenue à la boutique !</x-slot>
 <h1>Bonjour, client !</h1>
 <p>Nous sommes <b>{{ $shop_name }}</b>, avec des produits frais de <b>{{ $season }} </b>.</p>
    <h1>Bienvenue à la boutique de produits frais !</h1>
</x-layout>
```

#### B. Boucler sur les données avec `@foreach`

Mettez à jour `resources/views/products.blade.php` pour utiliser ces nouvelles variables et parcourir le tableau `produce`.

```html
{{-- resources/views/products.blade.php --}}
<x-layout> <x-slot name="header">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
    </x-slot>
    <ul class="divide-y divide-gray-200">
        @foreach ($produce as $item)
            <li class="py-4 flex justify-between items-center">
                <div>
                 <span class="text-lg font-semibold">{{ $item['name'] }}</span> </a>: <strong
                        class="text-green-600">${{ $item['price'] }}</strong> </div>
                @if ($item['in_stock'])
                    <span class="text-xs font-medium text-green-500">En stock</span>
                @else
                    <span class="text-xs font-medium text-red-500">Rupture</span>
                @endif
            </li>
        @endforeach
    </ul>
</x-layout>

```

## 3. Paramètres de route dynamiques (Détail d’un produit)

Pour voir les détails d’un produit, nous utilisons une **route dynamique**.

### A. Définir la route dynamique

Mettez à jour votre `routes/web.php` pour gérer un ID de produit dans l’URL.

```php
// routes/web.php (Ajoutez cette nouvelle route)

Route::get('/produce/{id}', function ($id) {
        $allProduce = [
        ['id' => 1, 'name' => 'Patate douce', 'price' => 2.99, 'description' => 'Parfait pour rôtir !'],
        ['id' => 2, 'name' => 'Pomme Granny Smith', 'price' => 1.50, 'description' => 'Acidulée et croquante.'],
        ['id' => 3, 'name' => 'Bouquet d’herbes fraîches', 'price' => 4.50, 'description' => 'Mélange de basilic, thym et romarin.'],
    ];

    //  Utilisez le helper Collection pour trouver l’élément par son ID
    $item = collect($allProduce)->first(fn($p) => $p['id'] == $id);

    return view('produce-detail', ['item' => $item]);
});
```

### B. Créer la vue de détail

Créez `resources/views/produce-detail.blade.php` pour afficher les données de l’élément.

```html
<x-layout>
    <x-slot name="header">
        <span class="text-3xl font-bold">{{ $item['name'] }}</span>
    </x-slot>
    <div class="space-y-4">
        <h2 class="text-xl font-bold text-green-700">${{ $item['price'] }}</h2>
        <p class="text-gray-700">{{ $item['description'] }}</p>
        <a href="/products" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800"> &larr;
            Retour à tous les produits </a>
    </div>
</x-layout>
```

### C. Lien vers la page de détail

Rendez le nom du produit cliquable dans la liste :

**Exemple dans `resources/views/products.blade.php` :**

```html
<a href="/product/{{ $item['id'] }}" class="text-blue-500 hover:underline">
    <span class="text-lg font-semibold">{{ $item['name'] }}</span>
</a>
```

### D. Refactorisation des données (Le "Pourquoi")

Actuellement, notre tableau de produits est défini dans `routes/web.php` pour la route `/products/{id}`. Mais notre route `/products` a aussi son propre tableau en dur. C’est une **duplication de données**, source de bugs et de maintenance difficile.

Corrigeons cela progressivement.

## Étape 1 : Centraliser le tableau**

Déplaçons d’abord le tableau complet en haut de `routes/web.php` pour qu’il soit partagé par les deux routes.

```php
// routes/web.php
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

$allProduce = [
    ['id' => 1, 'name' => 'Patate douce', 'price' => 2.99, 'in_stock' => true, 'description' => 'Parfait pour rôtir !'],
    ['id' => 2, 'name' => 'Pomme Granny Smith', 'price' => 1.50, 'in_stock' => true, 'description' => 'Acidulée et croquante.'],
    ['id' => 3, 'name' => 'Bouquet d’herbes fraîches', 'price' => 4.50, 'in_stock' => false, 'description' => 'Mélange de basilic, thym et romarin.'],
];

// ... autres routes ...

Route::get('/products', function () use ($allProduce) {
       return view('products', ['heading' => 'Nos produits frais', 'produce' => $allProduce]);
});

Route::get('/produce/{id}', function ($id) use ($allProduce) {
    $item = collect($allProduce)->first(fn($p) => $p['id'] == $id);

    return view('produce-detail', ['item' => $item]);
});
```

 C’est mieux ! Plus de duplication. Mais… mettre toutes nos données dans le fichier de routes reste brouillon. Si 10 routes en ont besoin, le fichier deviendra énorme. Il faut déplacer cette logique dans un endroit dédié aux _données_.

---

## 4. Refactorisation des données dans un modèle (La bonne méthode)

Cela nous amène au modèle **MVC (Modèle-Vue-Contrôleur)**.

### A. Comprendre MVC

**Modèle-Vue-Contrôleur (MVC)** est un modèle de conception qui sépare une application en trois composants :

- **Modèle :** Représente vos données et la logique métier. Il gère la récupération, le stockage et la gestion des données (ex : notre liste de produits).
- **Vue :** La couche présentation ; ce que l’utilisateur voit. Ce sont nos fichiers Blade (ex : `products.blade.php`).
- **Contrôleur :** Gère l’entrée utilisateur et l’interaction, faisant le lien entre Modèle et Vue. Dans notre cas simple, la **fonction anonyme de la route** (`function() { ... }`) fait office de contrôleur.

Notre tableau de données a clairement sa place dans un **Modèle**.

### B. Création du modèle `Product`

Dans Laravel, les modèles se trouvent dans le dossier `app/Models`.

1. **Créez le fichier :** Manuellement dans `app/Models/Product.php` ou via la commande Artisan : `php artisan make:model Product`.
2. **Ajoutez la logique :** Ouvrez le nouveau fichier et ajoutez une méthode statique pour contenir nos données.

```php
// app/Models/Product.php

namespace App\Models;

class Product
{
    public static function all(): array
    {
        return [
            ['id' => 1, 'name' => 'Patate douce', 'price' => 2.99, 'in_stock' => true, 'description' => 'Parfait pour rôtir !'],
            ['id' => 2, 'name' => 'Pomme Granny Smith', 'price' => 1.50, 'in_stock' => true, 'description' => 'Acidulée et croquante.'],
            ['id' => 3, 'name' => 'Bouquet d’herbes fraîches', 'price' => 4.50, 'in_stock' => false, 'description' => 'Mélange de basilic, thym et romarin.'],
        ];
    }
}
```

### C. Refactorisation des routes pour utiliser le modèle

Nous pouvons maintenant simplifier `routes/web.php`.

```php
// routes/web.php
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use App\Models\Product;


// 2. Mettre à jour la route /products
Route::get('/products', function () {
    return view('products', [
        'produce' => Product::all()
    ]);
});

// 3. Mettre à jour la route /produce/{id}
Route::get('/produce/{id}', function ($id) {
    $item = collect(Product::all())->first(fn($p) => $p['id'] == $id);

    return view('produce-detail', ['item' => $item]);
});
```

### ## 5. Améliorer le modèle avec une méthode "find"

Notre route `/products` est parfaite, mais `/produce/{id}` fait encore sa propre logique de recherche. Cette logique doit aussi aller dans le Modèle.

### A. Ajouter une méthode `find` au modèle

Modifiez `app/Models/Product.php` et ajoutez une méthode pour trouver un élément.

> [!hint] Nous utiliserons le helper Laravel `Arr::first`. N’oubliez pas de l’importer en haut du fichier : `use Illuminate\Support\Arr;`

```php
// app/Models/Product.php
namespace App\Models;
use Illuminate\Support\Arr;

class Product
{
    public static function find(int $id): ?array
    {
        return Arr::first(self::all(), fn($product) => $product['id'] == $id);
    }
}
```

### B. Refactorisation de la route de détail (encore)

Rendons la route `/produce/{id}` encore plus simple.

```php
// routes/web.php

Route::get('/produce/{id}', function ($id) {
    $item = Product::find($id);

    return view('produce-detail', ['item' => $item]);
});
```

## 6. Gérer le "cas triste"

Il reste un problème. Que se passe-t-il si vous visitez `/produce/99` ?

`Product::find(99)` retournera `null`. Notre vue `produce-detail.blade.php` essaiera alors d’accéder à `$item['name']` sur `null`, ce qui provoquera une erreur "Tentative d’accès à la propriété 'name' sur null". Mauvaise expérience utilisateur.

C’est le **"cas triste"** : quand tout ne se passe pas comme prévu.

On peut gérer cela élégamment avec le helper `abort` de Laravel.

### A. Implémenter `abort(404)`

Mettons à jour notre route finale pour la rendre "prête pour la production".

```php

Route::get('/produce/{id}', function ($id) {
    $item = Product::find($id);

    if (! $item) {
        abort(404);
    }

    return view('produce-detail', ['item' => $item]);
});
```

Désormais, si un utilisateur demande un produit inexistant, il verra une page "404 Not Found" professionnelle au lieu d’une erreur d’application.

---
