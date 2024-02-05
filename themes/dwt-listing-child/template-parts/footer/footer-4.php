<div class="cs-beforefooter">
    Got a food story? Share it with us!
    <div class="cs-button">
        <button>Send us an Email</button>
    </div>
</div>
<div class="cs-footer">
        <div class="container cs-container cs-footercontainer">
            <div class="flex-0 cs-footerinfo">
                <img src="/wp-content/uploads/2018/02/Tastyplates.png">
                <div>
                    Lorem ipsum dolor sit amet consectetur. Eu sa pien morbi egestas ligula aliquet quisque lacus faucibus. Faucibus sed aenean amet magna.
                </div>
                <div class="cs-footersocials">
                    Follow us
                    <span class="cs-spacer"></span>
                    <a href="#">
                        <img src="/wp-content/uploads/2018/02/mdi_instagram.png">
                    </a>

                    <a href="#">
                        <img src="/wp-content/uploads/2018/02/mdi_youtube.png">
                    </a>

                    <a href="#">
                        <img src="/wp-content/uploads/2018/02/mdi_facebook.png">
                    </a>
                </div>
            </div>
            <div class="flex-0 cs-footerlinks">
                <li>
                    <a href="#">About Tastyplates</a>
                </li>
                <li>
                    <a href="#">Careers</a>
                </li>
                <li>
                    <a href="#">Investor Guidelines</a>
                </li>
                <li>
                    <a href="#">Brand Guidelines</a>
                </li>
                <li>
                    <a href="#">Tastyplates for Business</a>
                </li>
                <li>
                    <a href="#">Notice</a>
                </li>
                <li>
                    <a href="#">Terms of Service</a>
                </li>
                <li>
                    <a href="#">Non-Member Use Policy</a>
                </li>
                <li>
                    <a href="#">Privacy Policy</a>
                </li>
                <li>
                    <a href="#">Terms of Location Based Services</a>
                </li>
                <li>
                    <a href="#">Community Guidelines</a>
                </li>
                <li>
                    <a href="#">Youth Protection Policy</a>
                </li>
                <li>
                    <a href="#">Holic Program</a>
                </li>
                <li>
                    <a href="#">Contact</a>
                </li>
            </div>
            <div class="flex-0 cs-footersubscribe">
                <h3>Subscribe to our newsletter</h3>
                <p>Get regular updates of events and hear latest stories from our team.</p>
                <div class="cs-footer-form">
                    <input type="text" placeholder="Provide Email here">
                    <button>Subscribe</button>
                </div>
            </div>
        </div>
        <div class="container cs-container cs-footercontainerlocations">
            Popular Locations : Hongdae  |  Ewha University  |  Itaewon  |  Gwanghwamun  |  Gangnam Station  |  Apgujeong  |  Cheongdam  |  Garosu-gil  |  Yeouido  |  Seorae Village  |  Samseong  |  Jamsil  |  Myeong-dong  |  Dongdaemun  |  Insa-dong  |  Seoul Station  |  Namsan  |  Jeju  |  Busan  |  Daegu  |  Gangwon-do  |  Incheon  |  Bundang
        </div>
</div>
<div class="cs-footer cs-copyright">
    <div class="container cs-container cs-footercontainercopyright">
            <div class="cs-copyrightleft">
                TASTYPLATES.CO<br>
                Melbourne, Australia<br>
                CEO: DR. MARK H.J. CHOI<br>
                Business License: 742-86-00224<br>
                E-commerce Business License: 2014-서울강남-01779<br>
                Customer Service: 02-565-5988

                <div class="cs-copyrighttext">
                    © 2023 MangoPlate Co., Ltd. All rights reserved.
                </div>
            </div>
            <div>
                한국어  |  English  |   简体中文
            </div>
         </div>

</div>


<script>
    var page = 2; // Initial page value

    jQuery(function($){
        $('#viewmorepost').on('click', function () {
        if (page <= 3) { // Limit the page load to page 3
            console.log('hi');
            var data = {
                action: 'load_more_posts',
                page: page,
            };

            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: data,
                success: function (response) {
                    if (response) {
                        $('#cs-recentreviews-container').append(response);
                        page++;
                    } else {
                        $('#viewmorepost').hide();
                    }
                }
            });
            if(page==3){
                $('#viewmorepost').hide(); // Hide viewmorepost after page 3
            }
        } else {
            $('#viewmorepost').hide(); // Hide viewmorepost after page 3
        }
    });
});
</script>