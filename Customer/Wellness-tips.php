<?php
session_start();
include 'Header.php';
include 'Nav.php';
?>

<!-- Wellness Tips Archive -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="fw-bold mb-3">Wellness Tips Archive</h1>
            <p class="lead text-muted">Browse our collection of science-backed mental wellness tips</p>
        </div>

        <!-- Filter Controls -->
        <div class="row mb-5">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="tip-search" placeholder="Search tips...">
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="category-filter">
                    <option value="all">All Categories</option>
                    <option value="meditation">Guided Meditation</option>
                    <option value="stress">Stress Management</option>
                    <option value="mindfulness">Mindfulness Training</option>
                    <option value="balance">Work-Life Balance</option>
                    <option value="relaxation">Relaxation Techniques</option>
                </select>
            </div>
        </div>

        <!-- Tips Grid -->
        <div class="row g-4" id="tips-container">
            <?php
            // Sample tip data
            $wellnessTips = [
                [
                    'title' => 'Body Scan Meditation',
                    'content' => 'Lie down and mentally scan your body from toes to head, noticing any tension and consciously relaxing each area.',
                    'category' => 'meditation'
                ],
                [
                    'title' => '5-4-3-2-1 Grounding Technique',
                    'content' => 'When feeling anxious, name: 5 things you can see, 4 things you can touch, 3 things you can hear, 2 things you can smell, and 1 thing you can taste.',
                    'category' => 'stress'
                ],
                [
                    'title' => 'Mindful Breathing',
                    'content' => 'Take 5 deep breaths: inhale for 4 seconds, hold for 4, exhale for 6. Repeat as needed throughout the day.',
                    'category' => 'mindfulness'
                ],
                [
                    'title' => 'Time Blocking Method',
                    'content' => 'Divide your day into blocks of time dedicated to specific tasks, including personal time and breaks.',
                    'category' => 'balance'
                ],
                [
                    'title' => 'Progressive Muscle Relaxation',
                    'content' => 'Tense and then relax each muscle group in your body, starting from your toes up to your forehead.',
                    'category' => 'relaxation'
                ],
                [
                    'title' => 'Loving-Kindness Meditation',
                    'content' => 'Send wishes of happiness, health, and peace to yourself and others with this guided practice.',
                    'category' => 'meditation'
                ],
                [
                    'title' => 'Journaling for Stress Relief',
                    'content' => 'Write down your thoughts and feelings for 10 minutes daily to process emotions and reduce stress.',
                    'category' => 'stress'
                ],
                [
                    'title' => 'Mindful Eating Exercise',
                    'content' => 'Focus completely on your food - its taste, texture, and smell - without distractions during one meal today.',
                    'category' => 'mindfulness'
                ]
            ];

            foreach ($wellnessTips as $tip) {
                $categoryClass = [
                    'meditation' => 'bg-primary bg-opacity-10 text-primary',
                    'stress' => 'bg-success bg-opacity-10 text-success',
                    'mindfulness' => 'bg-info bg-opacity-10 text-info',
                    'balance' => 'bg-warning bg-opacity-10 text-warning',
                    'relaxation' => 'bg-danger bg-opacity-10 text-danger'
                ][$tip['category']];
                
                $categoryName = [
                    'meditation' => 'Guided Meditation',
                    'stress' => 'Stress Management',
                    'mindfulness' => 'Mindfulness Training',
                    'balance' => 'Work-Life Balance',
                    'relaxation' => 'Relaxation Techniques'
                ][$tip['category']];
                
                echo '
                <div class="col-md-6 col-lg-4 col-xl-3 tip-card" data-category="'.$tip['category'].'">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <span class="badge '.$categoryClass.' mb-3">'.$categoryName.'</span>
                            <h5 class="card-title">'.$tip['title'].'</h5>
                            <p class="card-text text-muted">'.$tip['content'].'</p>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>

        <!-- Empty State (hidden by default) -->
        <div class="text-center py-5 d-none" id="no-results">
            <div class="mb-4">
                <i class="far fa-folder-open fa-4x text-muted"></i>
            </div>
            <h4 class="fw-bold mb-3">No Tips Found</h4>
            <p class="text-muted mb-4">Try adjusting your search or filter criteria</p>
            <button class="btn btn-primary" id="reset-filters">Reset Filters</button>
        </div>
    </div>
</section>

<!-- Popular Categories -->
<section class="py-5 bg-white">
    <div class="container py-5">
        <h2 class="fw-bold mb-5 text-center">Explore by Category</h2>
        
        <div class="row g-4">
            <div class="col-md-4 col-lg-2-4">
                <a href="?category=meditation" class="category-card card border-0 shadow-sm h-100 text-decoration-none hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3">
                            <i class="fas fa-spa fa-2x"></i>
                        </div>
                        <h3 class="h6 fw-bold mb-2">Guided Meditation</h3>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-lg-2-4">
                <a href="?category=stress" class="category-card card border-0 shadow-sm h-100 text-decoration-none hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-circle mx-auto mb-3">
                            <i class="fas fa-tint fa-2x"></i>
                        </div>
                        <h3 class="h6 fw-bold mb-2">Stress Management</h3>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-lg-2-4">
                <a href="?category=mindfulness" class="category-card card border-0 shadow-sm h-100 text-decoration-none hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-wrapper bg-info bg-opacity-10 text-info rounded-circle mx-auto mb-3">
                            <i class="fas fa-brain fa-2x"></i>
                        </div>
                        <h3 class="h6 fw-bold mb-2">Mindfulness Training</h3>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-lg-2-4">
                <a href="?category=balance" class="category-card card border-0 shadow-sm h-100 text-decoration-none hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-circle mx-auto mb-3">
                            <i class="fas fa-balance-scale fa-2x"></i>
                        </div>
                        <h3 class="h6 fw-bold mb-2">Work-Life Balance</h3>
                    </div>
                </a>
            </div>
            
            <div class="col-md-4 col-lg-2-4">
                <a href="?category=relaxation" class="category-card card border-0 shadow-sm h-100 text-decoration-none hover-lift">
                    <div class="card-body text-center p-4">
                        <div class="icon-wrapper bg-danger bg-opacity-10 text-danger rounded-circle mx-auto mb-3">
                            <i class="fas fa-couch fa-2x"></i>
                        </div>
                        <h3 class="h6 fw-bold mb-2">Relaxation Techniques</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tipSearch = document.getElementById('tip-search');
        const categoryFilter = document.getElementById('category-filter');
        const tipsContainer = document.getElementById('tips-container');
        const tipCards = document.querySelectorAll('.tip-card');
        const noResults = document.getElementById('no-results');
        const resetFilters = document.getElementById('reset-filters');
        
        // Check for URL category parameter
        const urlParams = new URLSearchParams(window.location.search);
        const categoryParam = urlParams.get('category');
        if (categoryParam) {
            categoryFilter.value = categoryParam;
            filterTips();
        }
        
        // Search and filter event listeners
        tipSearch.addEventListener('input', filterTips);
        categoryFilter.addEventListener('change', filterTips);
        resetFilters.addEventListener('click', resetFiltersHandler);
        
        function filterTips() {
            const searchTerm = tipSearch.value.toLowerCase();
            const category = categoryFilter.value;
            
            let visibleCount = 0;
            
            tipCards.forEach(card => {
                const matchesCategory = category === 'all' || card.getAttribute('data-category') === category;
                const textContent = card.textContent.toLowerCase();
                const matchesSearch = textContent.includes(searchTerm);
                
                if (matchesCategory && matchesSearch) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (visibleCount === 0) {
                tipsContainer.classList.add('d-none');
                noResults.classList.remove('d-none');
            } else {
                tipsContainer.classList.remove('d-none');
                noResults.classList.add('d-none');
            }
        }
        
        function resetFiltersHandler() {
            tipSearch.value = '';
            categoryFilter.value = 'all';
            filterTips();
            window.history.pushState({}, '', window.location.pathname);
        }
    });
</script>

<style>
    .tip-card .card {
        transition: all 0.3s ease;
    }
    
    .tip-card:hover .card {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .icon-wrapper {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .hover-lift {
        transition: all 0.3s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    @media (min-width: 992px) {
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }
</style>

<?php
include 'Footer.php';
?>