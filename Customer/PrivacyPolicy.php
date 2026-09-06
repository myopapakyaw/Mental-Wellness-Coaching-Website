<?php
session_start();
$pageTitle = "Privacy Policy | Velora Mental Wellness";
$currentPage = 'privacy';
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
                    <h1 class="display-5 fw-bold text-dark mb-3">Privacy Policy</h1>
                    <!-- <p class="text-muted">Last Updated: <?php echo date('F j, Y'); ?></p> -->
                    <div class="divider mx-auto bg-primary" style="width: 80px; height: 3px;"></div>
                    <p class="text-secondary mt-3">Your privacy is important to us. This policy explains how we collect, use, and protect your personal information in Myanmar.</p>
                </div>
                
                <!-- Policy Content -->
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-5">
                    <div class="card-body p-4 p-md-5">
                        <!-- Introduction -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">1. Introduction</h2>
                            <p class="text-secondary">Velora Mental Wellness ("we," "us," or "our") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our mental wellness services in Myanmar, including our website, mobile application, and related services (collectively, the "Services").</p>
                        </section>

                        <!-- Data Collection -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">2. Information We Collect</h2>
                            <p class="text-secondary">We may collect the following types of information:</p>
                            
                            <div class="mb-4">
                                <h3 class="h5 fw-bold text-dark mb-3">Personal Information</h3>
                                <ul class="text-secondary">
                                    <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Name, contact details (email, phone, address in Myanmar)</li>
                                    <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Payment information (processed securely)</li>
                                    <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Health information relevant to your wellness plan</li>
                                    <li><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Government-issued ID (for verification where required)</li>
                                </ul>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="h5 fw-bold text-dark mb-3">Location Data</h3>
                                <ul class="text-secondary">
                                    <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Delivery address and approximate location for service optimization</li>
                                    <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> IP address to determine general location</li>
                                    <li><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> GPS data (with your explicit consent for certain features)</li>
                                </ul>
                                <div class="alert alert-soft-primary rounded-2 py-3 mt-3">
                                    <p class="mb-0 text-secondary"><i class="fas fa-map-marker-alt text-primary me-2"></i> <strong>Myanmar Note:</strong> Location services help us provide better delivery estimates and localize our wellness recommendations for you.</p>
                                </div>
                            </div>
                            
                            <div>
                                <h3 class="h5 fw-bold text-dark mb-3">Usage Data</h3>
                                <ul class="text-secondary">
                                    <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Browser type and device information</li>
                                    <li class="mb-2"><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Pages visited and time spent on our services</li>
                                    <li><i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i> Preferences and settings</li>
                                </ul>
                            </div>
                        </section>

                        <!-- How We Use Information -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">3. How We Use Your Information</h2>
                            <p class="text-secondary">We use collected information to:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="text-secondary">
                                        <li class="mb-2">Provide and maintain our Services</li>
                                        <li class="mb-2">Process transactions in Myanmar Kyat (MMK)</li>
                                        <li class="mb-2">Deliver products to your Myanmar address</li>
                                        <li class="mb-2">Personalize your wellness experience</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="text-secondary">
                                        <li class="mb-2">Improve our Services for Myanmar users</li>
                                        <li class="mb-2">Communicate with you about orders</li>
                                        <li class="mb-2">Provide customer support</li>
                                        <li>Comply with legal obligations in Myanmar</li>
                                    </ul>
                                </div>
                            </div>
                        </section>

                        <!-- Data Sharing -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">4. Data Sharing and Disclosure</h2>
                            <p class="text-secondary">We may share information with:</p>
                            <div class="bg-light-soft rounded-2 p-3 mb-3">
                                <ul class="text-secondary mb-0">
                                    <li class="mb-2"><strong>Service Providers:</strong> Delivery partners in Myanmar, payment processors</li>
                                    <li class="mb-2"><strong>Healthcare Professionals:</strong> Only with your consent for treatment purposes</li>
                                    <li class="mb-2"><strong>Legal Authorities:</strong> When required by Myanmar law</li>
                                    <li><strong>Business Transfers:</strong> In case of merger or acquisition</li>
                                </ul>
                            </div>
                            <p class="text-secondary">We do not sell your personal information to third parties.</p>
                        </section>

                        <!-- Data Security -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">5. Data Security in Myanmar</h2>
                            <p class="text-secondary">We implement appropriate security measures including:</p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">SSL Encryption</span>
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">Secure Servers</span>
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">Access Controls</span>
                                <span class="badge bg-soft-primary text-primary py-2 px-3 rounded-1">Regular Audits</span>
                            </div>
                            <div class="alert alert-soft-warning rounded-2 py-3">
                                <p class="mb-0 text-secondary"><i class="fas fa-shield-alt text-primary me-2"></i> While we strive to protect your data, no method of transmission over the Internet is 100% secure. We cannot guarantee absolute security of your information in Myanmar or elsewhere.</p>
                            </div>
                        </section>

                        <!-- Data Retention -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">6. Data Retention</h2>
                            <p class="text-secondary">We retain personal information:</p>
                            <ul class="text-secondary">
                                <li class="mb-2">As long as your account is active</li>
                                <li class="mb-2">As needed to provide Services</li>
                                <li class="mb-2">To comply with Myanmar legal obligations</li>
                                <li>To resolve disputes and enforce agreements</li>
                            </ul>
                            <p class="text-secondary mt-3">Health-related data is retained for 7 years as recommended by medical guidelines, unless you request earlier deletion where permitted by law.</p>
                        </section>

                        <!-- Rights -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">7. Your Privacy Rights</h2>
                            <p class="text-secondary">You have the right to:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light-soft shadow-sm h-100">
                                        <div class="card-body">
                                            <ul class="text-secondary">
                                                <li class="mb-2">Access your personal data</li>
                                                <li class="mb-2">Correct inaccurate information</li>
                                                <li>Request deletion of your data</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light-soft shadow-sm h-100 mt-3 mt-md-0">
                                        <div class="card-body">
                                            <ul class="text-secondary">
                                                <li class="mb-2">Object to certain processing</li>
                                                <li class="mb-2">Request data portability</li>
                                                <li>Withdraw consent</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <p class="text-secondary">To exercise these rights, please contact us at <a href="mailto:privacy@veloramentalwellness.com" class="text-primary">privacy@veloramentalwellness.com</a>.</p>
                            </div>
                        </section>

                        <!-- Myanmar-Specific Provisions -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">8. Myanmar-Specific Provisions</h2>
                            <div class="bg-light-soft rounded-2 p-3">
                                <ul class="text-secondary mb-0">
                                    <li class="mb-2">All data is stored on servers located in Southeast Asia for faster access</li>
                                    <li class="mb-2">We comply with Myanmar's Electronic Transactions Law and other applicable regulations</li>
                                    <li class="mb-2">Local payment methods (WavePay, KBZ Pay, etc.) are processed through secure gateways</li>
                                    <li>Delivery partners are contractually obligated to protect your information</li>
                                </ul>
                            </div>
                        </section>

                        <!-- Cookies and Tracking -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">9. Cookies and Tracking Technologies</h2>
                            <p class="text-secondary">We use cookies and similar technologies to:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="text-secondary">
                                        <li class="mb-2">Authenticate users</li>
                                        <li class="mb-2">Remember preferences</li>
                                        <li>Analyze site usage</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="text-secondary">
                                        <li class="mb-2">Improve services</li>
                                        <li class="mb-2">Deliver targeted content</li>
                                        <li>Track order status</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="alert alert-soft-primary rounded-2 py-3 mt-3">
                                <p class="mb-0 text-secondary"><i class="fas fa-cookie text-primary me-2"></i> You can control cookies through your browser settings. However, disabling cookies may affect certain features of our Services.</p>
                            </div>
                        </section>

                        <!-- Children's Privacy -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">10. Children's Privacy</h2>
                            <p class="text-secondary">Our Services are not directed to children under 16. We do not knowingly collect personal information from children without parental consent. If we learn we have collected such information, we will delete it promptly.</p>
                        </section>

                        <!-- Policy Changes -->
                        <section class="mb-5">
                            <h2 class="h4 fw-bold text-dark mb-3">11. Changes to This Policy</h2>
                            <p class="text-secondary">We may update this Privacy Policy periodically. We will notify you of significant changes by posting the new policy on our website with an updated effective date.</p>
                        </section>

                        <!-- Contact -->
                        <section>
                            <h2 class="h4 fw-bold text-dark mb-3">12. Contact Us</h2>
                            <p class="text-secondary">For questions about this Privacy Policy or your personal data:</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h5 class="h6 mb-1 text-dark">Myanmar Office</h5>
                                            <p class="text-secondary mb-0">
                                                No. 456, Privacy Avenue<br>
                                                Kamayut Township, Yangon<br>
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
                                            <h5 class="h6 mb-1 text-dark">Data Protection Officer</h5>
                                            <p class="text-secondary mb-0">
                                                +95 9 876 543 210<br>
                                                dpo-myanmar@veloramentalwellness.com
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                
                <!-- Consent Note -->
                <div class="text-center text-muted small">
                    <p>By using our Services, you consent to the collection and use of your information as described in this Privacy Policy, including location-based services for delivery in Myanmar.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include('Footer.php'); ?>