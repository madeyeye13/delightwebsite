{{--
    Blog shared data — include this in any view that needs $posts
    Usage: @include('components.blog-data')
--}}
@php
$posts = [
    [
        'slug'     => 'hand-picked-stock-what-it-means',
        'title'    => 'Hand-Picked Stock: What It Actually Means for Your Fabric Order',
        'excerpt'  => 'Most fabric stores fill shelves just to fill them. We explain what our quality inspection process looks like — and why it matters for your final garment.',
        'author'   => 'Amara Obi',
        'date'     => '18 Mar 2024',
        'image'    => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&q=80',
        'tags'     => ['Fabrics', 'Quality'],
        'featured' => true,
    ],
    [
        'slug'     => 'aso-ebi-coordination-guide',
        'title'    => 'The Complete Aso-Ebi Coordination Guide for Families',
        'excerpt'  => 'Coordinating fabric across 30 people is harder than it sounds. Here\'s the system we\'ve seen work time and again for large family functions in Lagos.',
        'author'   => 'Chidera Eze',
        'date'     => '14 Mar 2024',
        'image'    => 'https://images.unsplash.com/photo-1594938298603-c8148c4b4357?w=800&q=80',
        'tags'     => ['Aso-Ebi', 'Style'],
        'featured' => false,
    ],
    [
        'slug'     => 'senator-fabric-vs-plain',
        'title'    => 'Senator Fabric vs Plain Cotton: Which Holds Up in Lagos Heat?',
        // ✅ After
        'excerpt'  => 'We tested both against Lagos weather for three weeks. The results were not what most people expect — especially if you\'re buying for outdoor events.',
        'author'   => 'Kemi Adeyemi',
        'date'     => '09 Mar 2024',
        'image'    => 'https://images.unsplash.com/photo-1620799140188-3b2a02fd9a77?w=800&q=80',
        'tags'     => ['Fabrics', 'Research'],
        'featured' => false,
    ],
    [
        'slug'     => 'lace-buying-mistakes',
        'title'    => 'Five Lace Buying Mistakes and How to Avoid Every One',
        'excerpt'  => 'Bad lace purchases are usually avoidable. From checking thread count to understanding finish types, this is the checklist we wish every buyer had from day one.',
        'author'   => 'Amara Obi',
        'date'     => '02 Mar 2024',
        'image'    => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&q=80',
        'tags'     => ['Lace', 'Buying Guide'],
        'featured' => false,
    ],
    [
        'slug'     => 'fashion-week-fabric-prep',
        'title'    => 'How Lagos Designers Prep Fabric Orders Before Fashion Week',
        'excerpt'  => 'The weeks before Lagos Fashion Week are a sprint. We talked to three designers about how they plan orders, manage shortfalls, and avoid the last-minute scramble.',
        'author'   => 'Chidera Eze',
        'date'     => '24 Feb 2024',
        'image'    => 'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80',
        'tags'     => ['Industry', 'Design'],
        'featured' => false,
    ],
    [
        'slug'     => 'fixed-pricing-explained',
        'title'    => 'Why We Use Fixed Pricing — and What That Means for You',
        'excerpt'  => 'Price negotiation is common in Lagos markets. We chose not to do it. Here\'s the reasoning, and why it actually saves buyers both time and money.',
        'author'   => 'Kemi Adeyemi',
        'date'     => '19 Feb 2024',
        'image'    => 'https://images.unsplash.com/photo-1579621970795-87facc2f976d?w=800&q=80',
        'tags'     => ['Pricing', 'Store'],
        'featured' => false,
    ],
    [
        'slug'     => 'ankara-vs-kente',
        'title'    => 'Ankara vs Kente: Understanding the Difference Before You Buy',
        'excerpt'  => 'They look similar on a shelf. They behave completely differently once a tailor starts working with them. This is the breakdown every buyer should read first.',
        'author'   => 'Amara Obi',
        'date'     => '11 Feb 2024',
        'image'    => 'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=800&q=80',
        'tags'     => ['Fabrics', 'Education'],
        'featured' => false,
    ],
];
@endphp