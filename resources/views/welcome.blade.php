<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Recess</title>
    <link href="{{asset('css/reset.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

    <script src="{{asset('js/jquery-3.4.1.min.js')}}" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js" type="text/javascript"></script>
    <style>
        .error {
            color: #ff7ca5 !important;
            margin-top: 1px;
        }
    </style>
</head>

<body>
    <!--Top Wrapper-->
    <div class="top_wrapper_bg">
        <header>
            <a href="#" class="logo"><img src="{{asset('images/logo.png')}}" alt=""></a>
            <div class="main_wrapper">
                <a href="#" class="innerlogo"><img src="{{asset('images/logo.png')}}" alt=""></a>
                <a href="javascript:void(0);" class="mobile_nav"> <span></span> <span></span> <span></span> </a>
                <nav>
                    <a href="javascript:void(0);" class="close"><img src="{{asset('images/close.png')}}" alt=""></a>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#about">About</a></li>
                        <!-- <li><a href="#download">Download</a></li> -->
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <!--First Fold-->
        <div class="first_fold">
            <div class="main_wrapper">
                <!--Content-->
                <div class="content_block">
                    <h2>RECESS</h2>
                    <h3>Activity. Anytime. Anywhere</h3>
                    <div class="paragraph">
                        <p>Activity Is All Around You!</p>
                        <p>Recess is the app that brings people together through the power of FUN and physical activity.
Recess lets you create, organize, and join recreational activities near you. Once an activity is created, users in the area can check it out and join!</p>
<p><strong>It’s Recess Time!</strong></p>
                    </div>
                    <!--Download-->
                    <!--<div class="download_block">
                <h4>DOWNLOAD NOW</h4>
                <p>Available on Stores. Download it now for FREE.</p>
                <div class="button_block">
                    <a href=""><img src="images/ios_button.png" alt=""></a>
                    <a href=""><img src="images/android_button.png" alt=""></a>
                </div>
            </div>-->
                    <!--End Download-->
                </div>
                <!--End Content-->
                <!--Image Holder-->
                <div class="photoholder"><img src="{{asset('images/feedbg.png')}}" alt=""></div>
                <!--End Image Holder-->
            </div>
        </div>
        <!--End First Fold-->
    </div>
    <!--End Top Wrapper-->

    <!--Features Wrapper-->
    <div class="feature_block" id="features">
        <div class="main_wrapper">
            <div class="title_block">
                <h2>Features</h2>
            </div>
            <!--Inner-->
            <div class="feature_inner_main_block">
                <!--Image Holder-->
                <div class="photoholder"><img src="{{asset('images/create_activity.png')}}" alt=""></div>
                <!--End Image Holder-->
                <!--Content-->
                <div class="content">
                    <div class="small_title">
                        <img src="{{asset('images/icon1.png')}}" alt="">
                        <h3>Create an Activity</h3>
                    </div>
                    <div class="innercontent">
                        <p>Activities are what Recess is all about. Create, invite, and participate in awesome activities with Recess users in your area.</p>
                        <ul>
                            <li>Creating an activity is simple and customizable</li>
                            <li>List of preloaded activities with rules</li>
                            <li>Customize size and location of activity</li>
                            <li>Ability to create "class", "tournament", or daily activity </li>
                            <li>Ability to create custom activity style</li>
                        </ul>
                    </div>
                </div>
                <!--End Content-->
            </div>
            <!--End Inner-->
        </div>

        <!--Find and Activity -->
        <div class="find_activity_block">
            <div class="main_wrapper">
                <div class="inner">
                    <!--Content-->
                    <div class="content">
                        <div class="small_title">
                            <img src="{{asset('images/icon2.png')}}" alt="">
                            <h3>Find and Join Activities</h3>
                        </div>
                        <div class="innercontent">
                            <p>Looking for something fun, rewarding, and challenging to participate in? Recess has got you covered!</p>
                            <ul>
                                <li>Location based Map and List views make finding activities simple</li>
                                <li>Endless types of activities can be joined</li>
                                <li>Meet people in your community who also enjoy living a healthy lifestyle</li>
                                <li>Games and classes range from beginner to expert</li>
                                <li>Find current day activities or planned activities in the future</li>
                            </ul>
                        </div>
                    </div>
                    <!--End Content-->

                    <!--Image Holder-->
                    <div class="photoholder"><img src="{{asset('images/activity_nearby.png')}}" alt=""></div>
                    <!--End Image Holder-->
                </div>
            </div>
        </div>
        <!--End Find and Activity -->

        <!--Community Feed-->
        <div class="community_feed_block">
            <div class="main_wrapper">
                <div class="inner">
                    <!--Image Holder-->
                    <div class="photoholder"><img src="{{asset('images/feedbg2.png')}}" alt=""></div>
                    <!--End Image Holder-->
                    <!--Content-->
                    <div class="content">
                        <div class="small_title">
                            <img src="{{asset('images/icon3.png')}}" alt="">
                            <h3>Community feed</h3>
                        </div>
                        <div class="innercontent">
                            <p>Share and view cool moments from every activity in your area. A great way to chat with like-minded people near you!</p>
                            <ul>
                                <li>Upload pics, videos, or text—and show off your awesome active lifestyle</li>
                                <li>Show your support by liking and commenting on friend’s posts</li>
                                <li>Cross-platform compatibility allows you to share posts on other social media</li>
                                <li>Explore and learn more about your community</li>
                            </ul>
                        </div>
                    </div>
                    <!--End Content-->
                </div>
            </div>
        </div>
        <!--End Community Feed-->
    </div>
    <!--End Features Wrapper-->

    <!--Become organizer-->
    <div class="become_organizer_block">
        <div class="main_wrapper">
            <div class="inner">
                <!--Content-->
                <div class="content">
                    <div class="small_title">
                        <img src="{{asset('images/icon4.png')}}" alt="">
                        <h3>Become an organizer</h3>
                    </div>
                    <div class="innercontent">
                        <p>Make money by organizing and charging for the activities you’ve created!</p>
                        <ul>
                            <li>Charge a participation fee</li>
                            <li>Create recurring events</li>
                            <li>Grow roots in your community</li>
                            <li>Easy and secure payment process</li>
                        </ul>
                    </div>
                </div>
                <!--End Content-->
                <!--Image Holder-->
                <div class="photoholder"><img src="{{asset('images/payment_bg.png')}}" alt=""></div>
                <!--End Image Holder-->
            </div>
        </div>
    </div>
    <!--End Become organizer-->

    <!--About-->
    <div class="about_block" id="about">
        <div class="main_wrapper">
            <div class="title_block">
                <h2>About Us</h2>
            </div>
            <p>Recess was created with a singular goal in mind: to help people have fun, make friends, and live a healthy lifestyle.</p>
            <p>We believe physical activity and recreation brings people together like nothing else in the world. That’s why we’ve made it easier than ever to plan, join, and share activities with your community. </p>
            <p>Connect with like-minded people, learn something new, make some extra cash, share your passion—whatever you’re looking for, you’ll find it on the Recess app.</p>
        </div>
    </div>
    <!--End About-->

    <!--Tour-->

    <div class="tour_block">
        <div class="main_wrapper">
            <div class="title_block">
                <h2>Take a Tour</h2>
            </div>
            <div class="video_spage">
                <video autoplay loop controls>
                    <source src="{{ asset('video/video.mp4') }}" type="video/mp4">
                    <source src="{{ asset('video/video.ogg') }}" type="video/ogg">
                </video>
            </div>
        </div>
    </div>

    <!--End Tour-->

    <!--Download-->
    <!--
<div class="download_block_bottom" id="download">
    <div class="main_wrapper">
        <div class="title_block">
            <h2>Download</h2>
        </div>
        <div class="inner">
        <div class="content">
            <h3>It’s a Free App.<br>DOWNLOAD NOW</h3>
            <h4>Recess brings the community together through recreation. Find. Join. Recreate.</h4>
            <div class="download_block">
                <p>Available on Stores. Download it now for FREE.</p>
                <div class="button_block">
                    <a href=""><img src="images/ios_button.png" alt=""></a>
                    <a href=""><img src="images/android_button.png" alt=""></a>
                </div>
            </div>
        </div>
        <div class="photoholder">
            <div class="appSlide owl-carousel owl-theme">
                <div class="item"><img src="images/appscreen2.jpg" alt=""></div>
                <div class="item"><img src="images/appscreen.jpg" alt=""></div>
                <div class="item"><img src="images/appscreen3.jpg" alt=""></div>
            </div>
        </div>
        </div>
    </div>
</div>
-->
    <!--End Download-->

    <!--Footer-->
    <footer id="contact">
        <div class="main_wrapper">
            <a href="" class="footerlogo"><img src="{{asset('images/footer_logo.png')}}" alt=""></a>
            <div class="footerinner">
                <!--Left-->
                <div class="footer_col">
                    <div class="topblock">
                        <div class="block">
                            <h3>Powered by</h3>
                            <p>Recess </p>
                            <div class="bottomblock">
                                <h3>Email</h3>
                                <a href="mailto:info@recessmobileapp.com">info@recessmobileapp.com</a>
                            </div>
                        </div>
                        <div class="block">
                            <h3>Quick Links</h3>
                            <ul>
                                <li><a href="#features">Features</a></li>
                                <li><a href="#about_us">About Us</a></li>
                                <!-- <li><a href="#download">Download</a></li> -->
                            </ul>
                        </div>
                        <div class="block">
                            <h3>Follow Us</h3>
                            <div class="social_icon">
                                <a href="" class="facebook">Facebook</a>
                                <a href="" class="twitter">Twitter</a>
                                <a href="" class="instagram">Instagram</a>
                            </div>
                        </div>
                    </div>

                </div>
                <!--End Left-->

                <!--Right-->
                <div class="footer_col">
                    <h3>Contact Us</h3>
                    <div class="frmholder" id="contactFormHolder">
                        <form id="contactForm">
                            <div class="input_holder">
                                <div class="innerholder">
                                    <div class="halfblock">
                                        <input type="text" name="name" id="name" placeholder="Name*" minlength="5" required>
                                    </div>
                                    <div class="halfblock">
                                        <input type="text" name="email" id="email" placeholder="Email*" required>
                                    </div>
                                </div>
                            </div>
                            <div class="input_holder">
                                <textarea name="message" id="message" placeholder="Message*" required minLength="10"></textarea>
                            </div>
                            <div class="input_holder"><input type="submit" name="submit_btn" value="Submit"></div>
                        </form>
                    </div>
                    <div class="frmholder" id="acknowledgement" style="display:none;">
                        <h3>Thank You for your interest, We will get back to you shortly</h3>
                    </div>
                </div>
                <!--End Right-->
            </div>
            <div class="copyright">© recess. 2021</div>
        </div>
    </footer>
    <!--End Footer-->


    <!-- Navigation -->
    <script>
        $(document).ready(function() {
            $('.mobile_nav').click(function() {
                $('nav').animate({
                    right: '0'
                }, 400);
            });
            $('.close, header nav ul li a').click(function() {
                $('nav').animate({
                    right: '-1200px'
                }, 400);
            });
        });
    </script>
    <!-- End Navigation -->

    <link rel="stylesheet" type="text/css" href="{{asset('css/animate.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/owl.carousel.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/owl.theme.default.min.css')}}">
    <script src="{{asset('js/owl.carousel.js')}}" type="text/javascript"></script>
    <script>
        $(() => {
            let contactForm = $("#contactForm");
            let contactFormHolder = $("#contactFormHolder");
            let acknowledgement = $("#acknowledgement");
            contactForm.validate({
                submitHandler: function(form) {
                    $(':input[type="submit"]').prop('disabled', true);
                    var inputs = $('#contactForm :input');
                    var values = {};
                    inputs.each(function() {
                        values[this.name] = $(this).val();
                    });
                    values['submitted_from'] = 'web';
                    delete values['submit_btn'];
                    $.ajax({
                        type: "POST",
                        url: "{{url('/api/v1/contact-us')}}",
                        data: values, // serializes the form's elements.
                        success: function(data) {
                            contactFormHolder.toggle();
                            acknowledgement.toggle();
                        },
                        error: function(err) {
                            console.error(err);
                            $(':input[type="submit"]').prop('disabled', false);
                            alert("Oops! something went wrong, please try again later.");
                        }
                    });
                }
            });
        });
        $('.appSlide').owlCarousel({
            autoplay: false,
            center: true,
            loop: true,
            nav: false,
            dots: false,
            margin: 0,
        });
    </script>

</html>