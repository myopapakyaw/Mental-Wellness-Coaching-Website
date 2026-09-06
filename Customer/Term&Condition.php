<?php
session_start();
$pageTitle = "Terms & Conditions | Velora Mental Wellness";
$currentPage = 'terms';
include("Header.php");
include("Nav.php");
?>

<!-- Main Content -->
<main class="bg-soft-primary min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <!-- Page Header -->
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold text-dark mb-3">Terms & Conditions</h1>
                    <!-- <p class="text-muted">Effective as of <?php echo date('F j, Y'); ?></p> -->
                    <div class="divider mx-auto bg-primary" style="width: 80px; height: 3px;"></div>
                </div>
                
                <!-- Terms Content -->
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-5">
                    <div class="card-body p-4 p-md-5">
                        <!-- Introduction -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">1. Introduction</h2>
                            <p class="text-secondary">Welcome to Velora Mental Wellness ("we," "our," or "us"). These Terms & Conditions outline the rules and regulations for using our website and services. By accessing this website, we assume you accept these terms in full.</p>
                        </section>

                        <!-- Location Services -->
                        <!-- <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">2. Location Services</h2>
                            <p class="text-secondary">For delivery and service optimization purposes, we may:</p>
                            <ul class="list-unstyled text-secondary">
                                <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Request access to your location (with your consent)</li>
                                <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Use IP address to determine approximate location</li>
                                <li><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Track order delivery progress in real-time</li>
                            </ul>
                            <div class="alert alert-soft-primary rounded-2 py-3 mt-3">
                                <p class="mb-0 text-secondary"><i class="fas fa-map-marker-alt text-primary me-2"></i> <strong>Note:</strong> Location data is used solely for service improvement and delivery purposes. You can disable location tracking in your browser or device settings at any time.</p>
                            </div>
                        </section> -->

                        <!-- Our Services -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">2. Our Services</h2>
                            <p class="text-secondary">Velora Mental Wellness provides:</p>
                            <ul class="list-unstyled text-secondary">
                                <!-- <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Mental wellness product delivery in Myanmar</li> -->
                                <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Online mental health consultations</li>
                                <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Digital wellness products</li>
                                <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Localized mental health resources</li>
                                <!-- <li><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Myanmar-language support</li> -->
                            </ul>
                        </section>

                        <!-- Delivery & Shipping
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">4. Delivery & Shipping in Myanmar</h2>
                            <p class="text-secondary">We deliver to all major regions in Myanmar:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="text-secondary">
                                        <li class="mb-2">Yangon Region</li>
                                        <li class="mb-2">Mandalay Region</li>
                                        <li class="mb-2">Naypyidaw Union Territory</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="text-secondary">
                                        <li class="mb-2">Sagaing Region</li>
                                        <li class="mb-2">Shan State</li>
                                        <li>Other major cities</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="alert alert-soft-warning rounded-2 py-3 mt-3">
                                <p class="mb-0 text-secondary"><i class="fas fa-truck text-primary me-2"></i> Delivery times may vary depending on your location in Myanmar. Remote areas may experience longer delivery times.</p>
                            </div>
                        </section> -->

                        <!-- User Responsibilities -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">3. User Responsibilities</h2>
                            <p class="text-secondary">By using our services, you agree to:</p>
                            <div class="bg-light-soft rounded-2 p-3">
                                <ul class="text-secondary mb-0">
                                    <li class="mb-2">Provide accurate delivery information</li>
                                    <li class="mb-2">Ensure someone is available to receive deliveries</li>
                                    <li class="mb-2">Provide correct contact information for delivery updates</li>
                                    <li>Notify us of any address changes promptly</li>
                                </ul>
                            </div>
                        </section>

                        <!-- Myanmar Office -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">4. Myanmar Operations</h2>
                            <p class="text-secondary">Our Myanmar office coordinates all local deliveries and services:</p>
                            <div class="card border-0 bg-light-soft shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="h6 mb-1 text-dark">Yangon Office</h5>
                                            <p class="text-secondary mb-0">
                                                No. 123, Wellness Road<br>
                                                Bahan Township, Yangon<br>
                                                Myanmar
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="h6 mb-1 text-dark">Contact</h5>
                                            <p class="text-secondary mb-0">
                                                +95 9 123 456 789<br>
                                                myanmar@veloramentalwellness.com
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Order Tracking -->
                        <!-- <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">7. Order Tracking</h2>
                            <p class="text-secondary">You can track your order through:</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">SMS Updates</span>
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">Email Notifications</span>
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">Mobile App</span>
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">Website Portal</span>
                            </div>
                            <p class="text-secondary">Real-time tracking includes:</p>
                            <ul class="text-secondary">
                                <li class="mb-2">Order processing status</li>
                                <li class="mb-2">Estimated delivery time</li>
                                <li>Delivery personnel contact information</li>
                            </ul>
                        </section> -->

                        <!-- Emergency Contacts -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">5. Myanmar Emergency Contacts</h2>
                            <div class="card border-primary shadow-sm">
                                <div class="card-header bg-primary text-white fw-bold py-3">
                                    <i class="fas fa-exclamation-circle me-2"></i> Local Emergency Services
                                </div>
                                <div class="card-body text-secondary py-3">
                                    <div class="mb-0">
                                        <div class="py-2"><strong>Police:</strong> 199</div>
                                        <div class="py-2"><strong>Fire:</strong> 191</div>
                                        <div class="py-2"><strong>Ambulance:</strong> 192</div>
                                        <div class="py-2"><strong>Mental Health Hotline:</strong> +95 9 262 240 737</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Governing Law -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">6. Governing Law</h2>
                            <p class="text-secondary">These terms shall be governed by and construed in accordance with the laws of the Republic of the Union of Myanmar.</p>
                        </section>

                        <!-- Contact -->
                        <section>
                            <h2 class="h4 fw-bold text-dark mb-3">7. Contact Information</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-globe-asia"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="h6 mb-1 text-dark">Myanmar Headquarters</h5>
                                            <p class="text-secondary mb-0">
                                                No. 123, Wellness Road<br>
                                                Bahan Township, Yangon<br>
                                                Myanmar
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="h6 mb-1 text-dark">Contact</h5>
                                            <p class="text-secondary mb-0">
                                                +95 9 123 456 789<br>
                                                myanmar-support@veloramentalwellness.com
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                
                <!-- Acceptance Note -->
                <div class="text-center text-muted small">
                    <p>By using our website and services, you acknowledge that you have read, understood, and agree to be bound by these Terms & Conditions, including our location tracking practices for delivery optimization in Myanmar.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('Footer.php'); ?>