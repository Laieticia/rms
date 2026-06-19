 <section id="menu">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="slbl">What's Cooking</span>
                <h2 class="stitle">Our Delicious <span>Menu</span></h2>
                <div class="sline"></div>
            </div>
            <!-- FIX 3 � filter buttons -->
            <div class="text-center mb-4" data-aos="fade-up">
                <button class="filtbtn active" data-f="all">All</button>
                <button class="filtbtn" data-f="burgers">Burgers</button>
                <button class="filtbtn" data-f="pizza">Pizza</button>
                <button class="filtbtn" data-f="chicken">Chicken</button>
                <button class="filtbtn" data-f="wraps">Wraps</button>
                <button class="filtbtn" data-f="desserts">Desserts</button>
                <button class="filtbtn" data-f="pasta">Pasta</button>
            </div>
            <div class="row g-4" id="mgrid">
                <!-- CARD 1: Burgers -->
                <div class="col-sm-6 col-lg-4 mwrap" data-c="burgers" data-aos="fade-up">
                    <div class="mcard" data-img="{{ url('website/assets/img/menu/1.jpg') }}"
                        data-title="Classic Smash Burger" data-cat="Burgers" data-price="$14.99" data-old="$18.99"
                        data-rating="4.9" data-reviews="128" data-cal="620" data-time="12"
                        data-desc="Double smashed patty, cheddar cheese, caramelized onions, house pickles and our legendary special sauce. Made fresh to order on a toasted brioche bun."
                        data-tags="Spicy,Bestseller,Beef">
                        <div class="mimg">
                            <img src="{{ url('website/assets/img/menu/1.jpg') }}" alt="Smash Burger" />
                            <div class="mbdg hot"><i class="fas fa-star"></i> Hot</div>
                            <div class="mhrt"><i class="far fa-heart"></i></div>
                        </div>
                        <div class="mbody">
                            <div class="mcat">Burgers</div>
                            <div class="mtit">Classic Smash Burger</div>
                            <div class="mdesc">Double smashed patty, cheddar, caramelized onions, pickles &amp;
                                special sauce</div>
                            <div class="mfoot">
                                <div>
                                    <div class="mprice">$14.99 <small>$18.99</small></div>
                                    <div class="mstars"><i class="fas fa-star"></i> <span
                                            style="color:#bbb;font-size:.7rem;">(128)</span></div>
                                </div>
                                <button class="madd" title="View Details"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CARD 2: Pizza -->
                <div class="col-sm-6 col-lg-4 mwrap" data-c="pizza" data-aos="fade-up" data-aos-delay="80">
                    <div class="mcard" data-img="{{ url('website/assets/img/menu/2.jpg') }}"
                        data-title="Margherita Royale" data-cat="Pizza" data-price="$19.99" data-old="$24.99"
                        data-rating="4.8" data-reviews="95" data-cal="480" data-time="18"
                        data-desc="San Marzano tomatoes, fresh buffalo mozzarella, fragrant basil leaves, drizzled with Italian truffle oil on a hand-stretched sourdough base."
                        data-tags="Vegetarian,New,Italian">
                        <div class="mimg">
                            <img src="{{ url('website/assets/img/menu/2.jpg') }}" alt="Pizza" />
                            <div class="mbdg new"><i class="fas fa-star"></i> New</div>
                            <div class="mhrt"><i class="far fa-heart"></i></div>
                        </div>
                        <div class="mbody">
                            <div class="mcat">Pizza</div>
                            <div class="mtit">Margherita Royale</div>
                            <div class="mdesc">San Marzano tomatoes, buffalo mozzarella, basil &amp; truffle oil on
                                sourdough</div>
                            <div class="mfoot">
                                <div>
                                    <div class="mprice">$19.99 <small>$24.99</small></div>
                                    <div class="mstars"><i class="fas fa-star"></i> <span
                                            style="color:#bbb;font-size:.7rem;">(95)</span></div>
                                </div>
                                <button class="madd" title="View Details"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CARD 3: Chicken -->
                <div class="col-sm-6 col-lg-4 mwrap" data-c="chicken" data-aos="fade-up" data-aos-delay="160">
                    <div class="mcard" data-img="{{ url('website/assets/img/menu/3.jpg') }}"
                        data-title="Nashville Hot Chicken" data-cat="Chicken" data-price="$12.99" data-old="$16.99"
                        data-rating="5.0" data-reviews="210" data-cal="710" data-time="15"
                        data-desc="Extra-crispy fried chicken tossed in our signature fiery Nashville spice blend, served with honey drizzle and house pickles on a toasted brioche bun."
                        data-tags="Spicy,Bestseller,Crispy">
                        <div class="mimg">
                            <img src="{{ url('website/assets/img/menu/3.jpg') }}" alt="Chicken" />
                            <div class="mbdg"><i class="fas fa-star"></i> Best Seller</div>
                            <div class="mhrt"><i class="far fa-heart"></i></div>
                        </div>
                        <div class="mbody">
                            <div class="mcat">Chicken</div>
                            <div class="mtit">Nashville Hot Chicken</div>
                            <div class="mdesc">Crispy fried chicken in fiery Nashville spice blend with honey drizzle
                            </div>
                            <div class="mfoot">
                                <div>
                                    <div class="mprice">$12.99 <small>$16.99</small></div>
                                    <div class="mstars"><i class="fas fa-star"></i> <span
                                            style="color:#bbb;font-size:.7rem;">(210)</span></div>
                                </div>
                                <button class="madd" title="View Details"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CARD 4: Wraps -->
                <div class="col-sm-6 col-lg-4 mwrap" data-c="wraps" data-aos="fade-up">
                    <div class="mcard" data-img="{{ url('website/assets/img/menu/4.jpg') }}"
                        data-title="Loaded Fajita Wrap" data-cat="Wraps" data-price="$10.99" data-old=""
                        data-rating="4.5" data-reviews="74" data-cal="520" data-time="10"
                        data-desc="Grilled chicken strips, saut�ed bell peppers and onions, sour cream, fresh guacamole and salsa wrapped in a warm flour tortilla with melted cheddar."
                        data-tags="Grilled,Fresh,Mexican">
                        <div class="mimg">
                            <img src="{{ url('website/assets/img/menu/4.jpg') }}" alt="Wrap" />
                            <div class="mhrt"><i class="far fa-heart"></i></div>
                        </div>
                        <div class="mbody">
                            <div class="mcat">Wraps</div>
                            <div class="mtit">Loaded Fajita Wrap</div>
                            <div class="mdesc">Grilled chicken, peppers, sour cream &amp; guacamole in a warm tortilla
                            </div>
                            <div class="mfoot">
                                <div>
                                    <div class="mprice">$10.99</div>
                                    <div class="mstars"><i class="fas fa-star"></i> <span
                                            style="color:#bbb;font-size:.7rem;">(74)</span></div>
                                </div>
                                <button class="madd" title="View Details"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CARD 5: Desserts -->
                <div class="col-sm-6 col-lg-4 mwrap" data-c="desserts" data-aos="fade-up" data-aos-delay="80">
                    <div class="mcard" data-img="{{ url('website/assets/img/menu/5.jpg') }}"
                        data-title="Nutella Lava Cake" data-cat="Desserts" data-price="$8.99" data-old="$11.99"
                        data-rating="4.9" data-reviews="56" data-cal="390" data-time="8"
                        data-desc="Warm molten chocolate cake with a gooey Nutella center, served alongside Madagascar vanilla bean ice cream with salted caramel drizzle and fresh berries."
                        data-tags="Sweet,New,Chocolate">
                        <div class="mimg">
                            <img src="{{ url('website/assets/img/menu/5.jpg') }}" alt="Lava Cake" />
                            <div class="mbdg new"><i class="fas fa-star"></i> New</div>
                            <div class="mhrt"><i class="far fa-heart"></i></div>
                        </div>
                        <div class="mbody">
                            <div class="mcat">Desserts</div>
                            <div class="mtit">Nutella Lava Cake</div>
                            <div class="mdesc">Molten chocolate cake with Nutella center, vanilla ice cream &amp;
                                caramel</div>
                            <div class="mfoot">
                                <div>
                                    <div class="mprice">$8.99 <small>$11.99</small></div>
                                    <div class="mstars"><i class="fas fa-star"></i> <span
                                            style="color:#bbb;font-size:.7rem;">(56)</span></div>
                                </div>
                                <button class="madd" title="View Details"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- CARD 6: Pasta -->
                <div class="col-sm-6 col-lg-4 mwrap" data-c="pasta" data-aos="fade-up" data-aos-delay="160">
                    <div class="mcard" data-img="{{ url('website/assets/img/menu/6.jpg') }}"
                        data-title="Truffle Mushroom Pasta" data-cat="Pasta" data-price="$16.99" data-old=""
                        data-rating="4.9" data-reviews="88" data-cal="560" data-time="20"
                        data-desc="Al dente tagliatelle tossed with mixed wild mushrooms, freshly shaved black truffle, aged parmesan, fresh thyme and a touch of cream in garlic butter."
                        data-tags="Vegetarian,Chef's Pick,Italian">
                        <div class="mimg">
                            <img src="{{ url('website/assets/img/menu/6.jpg') }}" alt="Pasta" />
                            <div class="mbdg hot">Chef's Pick</div>
                            <div class="mhrt"><i class="far fa-heart"></i></div>
                        </div>
                        <div class="mbody">
                            <div class="mcat">Pasta</div>
                            <div class="mtit">Truffle Mushroom Pasta</div>
                            <div class="mdesc">Al dente tagliatelle, wild mushrooms, black truffle, parmesan &amp;
                                thyme</div>
                            <div class="mfoot">
                                <div>
                                    <div class="mprice">$16.99</div>
                                    <div class="mstars"><i class="fas fa-star"></i> <span
                                            style="color:#bbb;font-size:.7rem;">(88)</span></div>
                                </div>
                                <button class="madd" title="View Details"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end #mgrid -->
            <div class="text-center mt-5"><a href="#" class="btn-red"><i class="fas fa-th-large"></i>View Full
                    Menu</a></div>
        </div>
    </section>