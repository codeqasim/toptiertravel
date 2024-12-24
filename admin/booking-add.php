<?php
require_once '_config.php';
auth_check();

$title = T::add .' '. T::booking;
include "_header.php";

?>
    <div class="page_head bg-transparent">
        <div class="panel-heading">
            <div class="float-start">
                <p class="m-0 page_title"><?=T::add.' '.T::booking?></p>
            </div>
            <div class="float-end">
                <a href="javascript:window.history.back();" data-toggle="tooltip" data-placement="top" title="Previous Page" class="loading_effect btn btn-warning"><?=T::back?></a>
            </div>
        </div>
    </div>

    <div class="mt-1">

    <div class="p-3">

    <div class="container">
        <form id="bookingForm">
            <!-- Select Hotel -->

            <div class="row g-3">
            <div class="col-md-3">
            <?php
                $locations = $db->select("hotels", "location", ["status" => 1, "GROUP" => "location"]);
            ?>

            <div class="form-floating mb-3">
                <select class="form-select" id="locationSelect" name="location" required>
                    <option value="" disabled selected>Select a Location</option>
                    <?php foreach($locations as $location) { ?>
                        <option value="<?= $location ?>"><?= $location ?></option>
                    <?php } ?>
                </select>
                <label for="locationSelect">Select Location</label>
            </div>
        </div>
            <div class="col-md-3">
            <div class="form-floating mb-3">
                <select class="form-select" id="hotelSelect" name="hotel" required>
                    <option value="" disabled selected>Select a Hotel</option>
                </select>
                <label for="hotelSelect">Select Hotel</label>
            </div>
            </div>

                <div class="col-md-3">

                <div class="form-floating mb-3">
                <select class="form-select" id="roomSelect" name="room" required>
                    <option value="" disabled selected>Select a Room</option>
                </select>
                <label for="roomSelect">Select Room</label>
            </div>

                </div>

                <div class="col-md-3">

                <div class="form-floating mb-3">
                <select class="form-select" id="roomOption" name="room_option" required>
                    <option value="" disabled selected>Select a Room</option>
                </select>
                <label for="roomSelect">Room Option</label>
            </div>

                </div>



            </div>

            <!-- Check-in and Check-out Dates -->
            <div class="row mb-3 g-3">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="calendar form-control" id="checkinDate" name="checkin" required>
                        <label for="checkinDate">Check-in Date</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="calendar form-control" id="checkoutDate" name="checkout" required>
                        <label for="checkoutDate">Check-out Date</label>
                    </div>
                </div>
            </div>

            <!-- Number of Travelers -->
            <div class="row mb-3 g-3">
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="numTravelers" name="travelers" min="1" value="1" required>
                        <label for="numTravelers">Number of Travelers</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="numAdults" name="adults" min="1" value="1" required>
                        <label for="numAdults">Adults</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-floating">
                        <input type="number" class="form-control" id="numChildren" name="children" min="0" value="0">
                        <label for="numChildren">Children</label>
                    </div>
                </div>
            </div>

            <!-- Booking Price -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="bookingPrice" name="price" placeholder="Enter booking price" required>
                <label for="bookingPrice">Total Price</label>
            </div>

            <!-- Client Details -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="clientName" name="name" placeholder="Enter full name" required>
                <label for="clientName">Client Name</label>
            </div>

            <div class="row g-3">

            <div class="col-md-6">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="clientEmail" name="email" placeholder="Enter email address" required>
                <label for="clientEmail">Email</label>
            </div>
            </div>

            <div class="col-md-6">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="clientEmail" name="email" placeholder="Enter email address" required>
                <label for="clientEmail">Phone</label>
            </div>
            </div>
            </div>


            <div class="d-block"></div>
            <hr>

            <div class="row">
            <div class="col-md-2">


            <div class="form-check d-flex gap-3 align-items-center">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
            <label class="form-check-label" for="flexCheckDefault">
                Send Email
            </label>
            </div>
            </div>

            <div class="col-md-2">

            <div class="form-check d-flex gap-3 align-items-center">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
            <label class="form-check-label" for="flexCheckDefault">
                Send SMS
            </label>
            </div>
            </div>

            <div class="col-md-2">

            <div class="form-check d-flex gap-3 align-items-center">
            <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" checked>
            <label class="form-check-label" for="flexCheckDefault">
                Send Whatsapp
            </label>
            </div>
            </div>

            </div>

            <hr>


            <!-- Submit Button -->
            <div class="text-start">
                <button type="submit" class="btn btn-primary">Submit Booking</button>
            </div>
        </form>
    </div>


        </div>
    </div>

    <script>
$(document).ready(function() {
    const hotelSelect = $('#hotelSelect');
    const roomSelect = $('#roomSelect');
    const roomOption = $('#roomOption');

    $('#locationSelect').on('change', function() {
        const location = $(this).val();
        if (location) {
            hotelSelect.prop('disabled', false);
            $.ajax({
                url: 'booking-ajax.php',
                type: 'POST',
                data: {
                    action: 'get_hotels',
                    location: location
                },
                success: function(response) {
                    hotelSelect.html('<option value="" disabled selected>Select a Hotel</option>');
                    if (response.status === 'success') {
                        response.hotels.forEach(function(hotel) {
                            hotelSelect.append(`<option value="${hotel.id}">${hotel.name}</option>`);
                        });
                    } else {
                        console.error('Error fetching hotels:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                }
            });
        } else {
            hotelSelect.prop('disabled', true).html('<option value="" disabled selected>Select a Hotel</option>');
        }
    });

    hotelSelect.on('change', function() {
        const hotelId = $(this).val();
        if (hotelId) {
            roomSelect.prop('disabled', false);
            $.ajax({
                url: 'booking-ajax.php',
                type: 'POST',
                data: {
                    action: 'get_rooms',
                    hotel_id: hotelId
                },
                success: function(response) {
                    roomSelect.html('<option value="" disabled selected>Select a Room</option>');
                    if (response.status === 'success') {
                        response.rooms.forEach(function(room) {
                            roomSelect.append(`<option value="${room.id}">${room.name}</option>`);
                        });
                    } else {
                        console.error('Error fetching rooms:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                }
            });
        } else {
            roomSelect.prop('disabled', true).html('<option value="" disabled selected>Select a Room</option>');
        }
    });

    roomSelect.on('change', function() {
        const roomId = $(this).val();
        if (roomId) {
            roomOption.prop('disabled', false);
            $.ajax({
                url: 'booking-ajax.php',
                type: 'POST',
                data: {
                    action: 'get_room_options',
                    room_id: roomId
                },
                success: function(response) {
                    roomOption.html('<option value="" disabled selected>Select a Room Option</option>');
                    if (response.status === 'success') {
                        response.options.forEach(function(option) {
                            roomOption.append(`<option value="${option.id}">${option.price} - Adults ${option.adults} Childs ${option.childs}</option>`);
                        });
                    } else {
                        console.error('Error fetching room options:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                }
            });
        } else {
            roomOption.prop('disabled', true).html('<option value="" disabled selected>Select a Room Option</option>');
        }
    });
});
</script>


<?php include "_footer.php" ?>
