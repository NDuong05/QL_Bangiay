           <!-- CATALOGUE -->
            <div class="catalogue-container always-active-page" id="catalogue">
                <!-- BANNER -->
                <div class="banner">
                    <div class="banner-images"></div> <!-- Images will be dynamically inserted here -->

                    <!-- Banner Navigation Buttons (Arrows) -->
                    <div class="banner-buttons">
                        <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
                        <button class="next" onclick="moveSlide(1)">&#10095;</button>
                    </div>
                </div>
                <div class="catalogue-name" id="display-catalogue-name">HOME</div>
                <div class="search-bar" style="display:none">
                    <label for="search-bar"><i class="fas fa-search"></i></label>
                    <input class="form-input-bar filter-option" type="text" name="search-bar" id="search-bar"
                        placeholder="Search products by name">
                </div>
                <div class="details-search-bar hide-on-pc show-on-mobile"
                    onclick="toggleModal('details-search-sidebar')">
                    <i class="fa-solid fa-bars"></i>
                    <p>Filter by</p>
                </div>
                <div class="catalogue-info" style="display:none">
                    <div class="products-amount">
                       <?php
                            
                            echo ' <p><span id="display-catalogue-amount">' . count($productlist) . '</span> product(s)</p>';
                       ?>
                    </div>
                    <div class="sortby">
                        <div>
                            <span>Sort by:</span>
                            <span id="sortby-mode-display">Alphabetically, A-Z</span>
                            <span class="dropdown-arrow">&#9662;</span>
                        </div>
                        <!-- Hidden checkbox to control the dropdown -->
                        <div class="container float-dropdown">
                            <ul class="menu-list">
                                <li class="sortby-option"><a>Alphabetically, A-Z</a></li>
                                <li class="sortby-option"><a>Alphabetically, Z-A</a></li>
                                <li class="sortby-option"><a>Price, low to high</a></li>
                                <li class="sortby-option"><a>Price, high to low</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- CATALOGUE - DETAILS SEARCH SIDEBAR -->
                
                <!-- CATALOGUE - MAIN -->
                <div class="main-container">
                
                    <!-- DISPLAY PRODUCTS -->
                    
                </div>
            </div>
            
            





