<div id="rooms"></div>
<div class="row">
    <div class="col-auto">
        <h3 class="text-22 fw-500">Available Rooms</h3>
    </div>
</div>
<div class="row g-3">
    <?php foreach ($hotel->rooms as $room) {
        // Check if image exists
        (@getimagesize(root . "uploads/" . $room->img)) ? $img = root . "uploads/" . $room->img : $img = root . "assets/img/hotel.jpg";
    ?>
        <?php foreach ($room->options as $opt => $options) { ?>
            <div class="border-light rounded px-30 py-30 sm:px-20 sm:py-20 mt-20">
                <div class="row y-gap-20">
                    <div class="col-12">
                        <h3 class="text-18 fw-500 mb-15"><?= $room->name ?></h3>
                        <div class="roomGrid">
                            <div class="roomGrid__header">
                                <div>Room Type</div>
                                <div>Benefits</div>
                                <div>Sleeps</div>
                                <div>Price For A Room</div>
                                <div>Select Rooms</div>
                                <div></div>
                            </div>

                            <form action="<?= root ?>hotels/booking" method="POST">

                            <div class="roomGrid__grid">
                                <div>
                                    <div class="ratio ratio-1:1">
                                        <img data-fancybox="gallery" data-src="<?= $img ?>" class="img-ratio rounded" src="<?= $img ?>" alt="room" style="object-fit: cover;">
                                    </div>
                                    <div class="y-gap-5 mt-20">
                                        <?php foreach ($room->amenities as $amenity) { ?>
                                            <div class="d-flex items-center">
                                                <i class="icon-check text-20 mr-10"></i>
                                                <div class="text-15"><?= $amenity ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="y-gap-30">
                                    <div class="roomGrid__content">
                                        <div>
                                            <div class="text-15 fw-500 mb-10">Your price includes:</div>
                                            <div class="y-gap-8">
                                                <?php if ($options->breakfast) { ?>
                                                    <div class="d-flex items-center text-green-2">
                                                        <i class="icon-check text-12 mr-10"></i>
                                                        <div class="text-15">Free Breakfast</div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($options->cancellation) { ?>
                                                    <div class="d-flex items-center text-green-2">
                                                        <i class="icon-check text-12 mr-10"></i>
                                                        <div class="text-15">Free Cancellation</div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex items-center text-light-1">
                                                <div class="row my-2">
                                                    <div class="col-md-12 px-3 align-items-center">
                                                        <!-- Adults count -->
                                                        <span class="mx-2"><?= $options->adults ?></span>
                                                        <!-- First SVG Icon -->
                                                        <svg height="20px" width="20px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                            <g>
                                                                <path d="M223.092,102.384c36.408,0,43.7-28.298,43.7-43.732V43.724c0-15.434-7.292-43.724-43.7-43.724 c-36.408,0-43.699,28.29-43.699,43.724v14.928C179.393,74.086,186.684,102.384,223.092,102.384z"/>
                                                                <path d="M332.816,204.792l-33.502-54.597c-9.812-16.012-22.516-28.507-40.535-28.507h-27.463h-39.106 c-29.406,0-53.24,25.255-53.24,56.404v162.192h35.992V512h34.834l21.521-156.443L252.836,512h34.834V263.901v-40.857l29.47,34.048 l55.89-38.978v-32.932L332.816,204.792z"/>
                                                            </g>
                                                        </svg>
                                                        <!-- Children count -->
                                                        <span class="mx-2"><?= $options->child ?></span>
                                                        <svg fill="#000000" width="15px" height="15px" viewBox="-64 0 512 512" xmlns="http://www.w3.org/2000/svg">
                  <path d="M120 72c0-39.765 32.235-72 72-72s72 32.235 72 72c0 39.764-32.235 72-72 72s-72-32.236-72-72zm254.627 1.373c-12.496-12.497-32.758-12.497-45.254 0L242.745 160H141.254L54.627 73.373c-12.496-12.497-32.758-12.497-45.254 0-12.497 12.497-12.497 32.758 0 45.255L104 213.254V480c0 17.673 14.327 32 32 32h16c17.673 0 32-14.327 32-32V368h16v112c0 17.673 14.327 32 32 32h16c17.673 0 32-14.327 32-32V213.254l94.627-94.627c12.497-12.497 12.497-32.757 0-45.254z"/>
               </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-18 lh-15 fw-500"><?= currency ?> <?= $options->price ?></div>
                                            <div class="text-14 lh-18 text-light-1">Includes taxes and charges</div>
                                        </div>
                                        <div>
                                            <select name="room_quantity" class="form-select px-4 py-2 mt-2 border room-quantity-select" style="height:52px" data-price="<?= $options->price ?>" onchange="updateTotalPrice(this)">
                                                <?php
                                                (isset($options->quantity)) ? $quantity = $options->quantity : $quantity = 1;
                                                for ($i = 1; $i <= $quantity; $i++) {
                                                ?>
                                                    <option class="" value="<?= $i ?>"><?= $i ?> - <?= currency ?> <?= $i * $options->price ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-14 lh-1">
                                        Room for
                                    </div>
                                    <div class="text-22 fw-500 lh-17 mt-5 total-price">
                                        <?= currency ?> <?= $options->price ?>
                                    </div>
                                    
                                        <?php
                                        $payload = [
                                            "supplier_name" => $hotel->supplier_name,
                                            "hotel_id" => $hotel->id,
                                            "hotel_name" => $hotel->name,
                                            "hotel_img" => $hotel->img[0],
                                            "hotel_address" => $hotel->city . "&nbsp;" . $hotel->address,
                                            "hotel_stars" => $hotel->stars,
                                            "room_id" => $room->id,
                                            "room_type" => $room->name,
                                            "currency" => currency,
                                            "room_price" => $options->price,
                                            "real_price" => $options->price,
                                            "checkin" => $meta['checkin'],
                                            "checkout" => $meta['checkout'],
                                            "adults" => $meta['adults'],
                                            "childs" => $meta['childs'],
                                            "supplier" => $hotel->supplier_name,
                                            "nationality" => $meta['nationality'],
                                            "city_name" => $hotel->city,
                                            "latitude" => $hotel->latitude,
                                            "longitude" => $hotel->longitude,
                                            "booking_data" => $options,
                                            "adult_travellers" => $hotel->hotel_phone,
                                            "child_travellers" => $hotel->hotel_phone,
                                            "hotel_phone" => $hotel->hotel_phone,
                                            "hotel_email" => $hotel->hotel_email,
                                            "hotel_website" => $hotel->hotel_website,
                                            "children_ages" => "",
                                            "cancellation_policy" => $hotel->cancellation,
                                            "room_data" => $room->options[0],
                                        ];
                                        ?>
                                        <input name="payload" type="hidden" value="<?= base64_encode(json_encode($payload)) ?>">
                                        <button type="submit" class="button h-50 px-24 -dark-1 bg-blue-1 text-white mt-10">Reserve <div class="icon-arrow-top-right ml-15"></div></button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<script>
    function updateTotalPrice(selectElement) {
        const pricePerRoom = parseFloat(selectElement.getAttribute('data-price'));
        const selectedQuantity = parseInt(selectElement.value);
        const totalPriceElement = selectElement.closest('.roomGrid__grid').querySelector('.total-price');

        totalPriceElement.innerHTML = "<?= currency ?> " + (pricePerRoom * selectedQuantity).toFixed(2);

        const formElement = selectElement.closest('.roomGrid__grid').querySelector('form');
        const payloadInput = formElement.querySelector('input[name="payload"]');

        if (payloadInput) {
           
            let payload = JSON.parse(atob(payloadInput.value));
            payload.room_price = pricePerRoom * selectedQuantity; 
            payload.real_price = pricePerRoom * selectedQuantity; 

            payloadInput.value = btoa(JSON.stringify(payload));
        }
    }
</script>
