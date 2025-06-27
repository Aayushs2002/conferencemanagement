@extends('backend.layouts.society.main')
@section('title')
    Payment Setting
@endsection
@section('content')
    <div class="main-content">
        <div class="breadcrumb">
            <h3>Payment Setting</h3>
        </div>
        <div class="separator-breadcrumb border-top mb-4"></div>
        <div class="col-md-12 my-4">
            <div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4 position-relative p-3" style="background-color: #D9D8D4">
                            <label for="national">
                                <h5 class="text-dark mt-2">Payment Setting For National</h5>
                                <div class="position-absolute" style="bottom: 40px; right: 20px;">

                                    <input class="form-check-input" type="radio" name="payment" value="national"
                                        id="national" style="transform: scale(2);" checked>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-4 position-relative p-3" style="background-color: #D9D8D4">
                            <label for="international">
                                <h5 class="text-dark mt-2">Payment Setting For International</h5>
                                <div class="position-absolute" style="bottom: 40px; right: 20px;">
                                    <input class="form-check-input" type="radio" name="payment" value="international"
                                        id="international" style="transform: scale(2);">
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class=" mb-4">
                <div class="card-body">
                    <form id="paymentForm">
                        <input type="hidden" id="currentNationalTab" value="fonepay">
                        <input type="hidden" id="currentInternationalTab" value="himalayan_bank">
                        <div class="" id="nationalSection">
                            <ul class="nav nav-pills" id="myPillTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#fonePIll" role="tab" aria-controls="fonePIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>FonePay</a></li>
                                <li class="nav-item"><a class="nav-link" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#mocoPIll" role="tab" aria-controls="mocoPIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>Moco</a></li>
                                <li class="nav-item"><a class="nav-link" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#esewaPIll" role="tab" aria-controls="esewaPIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>Esewa</a></li>
                                <li class="nav-item"><a class="nav-link" id="home-icon-pill" data-bs-toggle="pill"
                                        href="#khaltiPIll" role="tab" aria-controls="khaltiPIll" aria-selected="true"><i
                                            class="nav-icon i-Home1 mr-1"></i>Khalti</a></li>
                                <li class="nav-item"><a class="nav-link" id="profile-icon-pill" data-bs-toggle="pill"
                                        href="#bankPIll" role="tab" aria-controls="bankPIll" aria-selected="false"><i
                                            class="nav-icon i-Home1 mr-1"></i>Account Details</a></li>
                            </ul>
                            <div class="tab-content  mt-4" id="myPillTabContent">
                                <div class="tab-pane fade show active" id="fonePIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        {{-- <input type="hidden" name="conference_id" value="{{$conference->id}}"> --}}
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="profile_id">Profile Id <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('profile_id') is-invalid @enderror profile_id"
                                                name="profile_id" id="profile_id"
                                                value="{{ $nationalPayment ? $nationalPayment->profile_id : '' }}" />
                                            <div class="text-danger" id="profileIdError"></div>
                                            @error('profile_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="secret_key">Shared Secret Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('secret_key') is-invalid @enderror secret_key"
                                                name="secret_key" id="secret_key"
                                                value="{{ $nationalPayment ? $nationalPayment->secret_key : '' }}" />
                                            <div class="text-danger" id="secretKeyError"></div>

                                            @error('secret_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $nationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="mocoPIll" role="tabpanel"
                                    aria-labelledby="profile-icon-pill">
                                    <div class="row">
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_merchant_id">Merchant Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_merchant_id') is-invalid @enderror moco_merchant_id"
                                                name="moco_merchant_id" id="moco_merchant_id"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_merchant_id : '' }}" />
                                            <div class="text-danger" id="mocoMerchantIdError"></div>
                                            @error('moco_merchant_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_outlet_id">Outlet Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_outlet_id') is-invalid @enderror moco_outlet_id"
                                                name="moco_outlet_id" id="moco_outlet_id"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_outlet_id : '' }}" />
                                            <div class="text-danger" id="mocoOutletIdError"></div>
                                            @error('moco_outlet_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_terminal_id">Terminal Id
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_terminal_id') is-invalid @enderror moco_terminal_id"
                                                name="moco_terminal_id" id="moco_terminal_id"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_terminal_id : '' }}" />
                                            <div class="text-danger" id="mocoTerminalIdError"></div>
                                            @error('moco_terminal_id')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="moco_shared_key">Shared Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('moco_shared_key') is-invalid @enderror moco_shared_key"
                                                name="moco_shared_key" id="moco_shared_key"
                                                value="{{ $nationalPayment ? $nationalPayment->moco_shared_key : '' }}" />
                                            <div class="text-danger" id="mocoSharedKeyError"></div>
                                            @error('moco_shared_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData" id="submitData">
                                                {{ $nationalPayment ? 'Update' : 'Save' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="esewaPIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        {{-- <input type="hidden" name="conference_id" value="{{$conference->id}}"> --}}
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="esewa_product_code">Product Code
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('esewa_product_code') is-invalid @enderror esewa_product_code"
                                                name="esewa_product_code" id="esewa_product_code"
                                                value="{{ $nationalPayment ? $nationalPayment->esewa_product_code : '' }}" />
                                            <div class="text-danger" id="productCodeError"></div>
                                            @error('esewa_product_code')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="esewa_secret_key">Secret Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('esewa_secret_key') is-invalid @enderror esewa_secret_key"
                                                name="esewa_secret_key" id="esewa_secret_key"
                                                value="{{ $nationalPayment ? $nationalPayment->esewa_secret_key : '' }}" />
                                            <div class="text-danger" id="esewaSecretKeyError"></div>

                                            @error('esewa_secret_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $nationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="khaltiPIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        {{-- <input type="hidden" name="conference_id" value="{{$conference->id}}"> --}}
                                        <input type="hidden" name="id" id="id"
                                            value="{{ $nationalPayment ? $nationalPayment->id : '' }}">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="khalti_live_secret_key">Live Secret Key
                                                <code>*</code></label>
                                            <input type="text"
                                                placeholder="example: live_secret_key_68791341fdd94846a146f0457ff7b455"
                                                class="form-control @error('khalti_live_secret_key') is-invalid @enderror khalti_live_secret_key"
                                                name="khalti_live_secret_key" id="khalti_live_secret_key"
                                                value="{{ $nationalPayment ? $nationalPayment->khalti_live_secret_key : '' }}" />
                                            <div class="text-danger" id="khaltiLiveSecretKeyError"></div>

                                            @error('khalti_live_secret_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $nationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="bankPIll" role="tabpanel"
                                    aria-labelledby="profile-icon-pill">
                                    Etsyas
                                    beer, iphone skateboard locavore.
                                </div>

                            </div>
                        </div>
                        <div class="" id="internationalSection" style="display: none;">
                            <ul class="nav nav-pills" id="myPillTab" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="home-icon-pill"
                                        data-bs-toggle="pill" href="#homePIll" role="tab" aria-controls="homePIll"
                                        aria-selected="true"><i class="nav-icon i-Home1 mr-1"></i>Himalayan Bank</a></li>
                                <li class="nav-item"><a class="nav-link" id="profile-icon-pill" data-bs-toggle="pill"
                                        href="#profilePIll" role="tab" aria-controls="profilePIll"
                                        aria-selected="false"><i class="nav-icon i-Home1 mr-1"></i>Account Details</a>
                                </li>

                            </ul>
                            <div class="tab-content mt-4" id="myPillTabContent">
                                <div class="tab-pane fade show active" id="homePIll" role="tabpanel"
                                    aria-labelledby="home-icon-pill">
                                    <div class="row">
                                        <input type="hidden" name="international_id" id="international_id"
                                            value="{{ $internationalPayment ? $internationalPayment->id : '' }}">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="merchant_key">Merchant Key
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('merchant_key') is-invalid @enderror merchant_key"
                                                name="merchant_key" id="merchant_key"
                                                value="{{ $internationalPayment ? $internationalPayment->merchant_key : '' }}" />
                                            <div class="text-danger" id="merchantKeyError"></div>
                                            @error('merchant_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="api_key">Api Key <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('api_key') is-invalid @enderror api_key"
                                                name="api_key" id="api_key"
                                                value="{{ $internationalPayment ? $internationalPayment->api_key : '' }}" />
                                            <div class="text-danger" id="apiKeyError"></div>
                                            @error('api_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="access_token">AccessToken
                                                <code>*</code></label>
                                            <input type="text"
                                                class="form-control @error('access_token') is-invalid @enderror access_token"
                                                name="access_token" id="access_token"
                                                value="{{ $internationalPayment ? $internationalPayment->access_token : '' }}" />
                                            <div class="text-danger" id="accessTokenError"></div>
                                            @error('access_token')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label"
                                                for="merchant_signing_private_key">MerchantSigningPrivateKey
                                                <code>*</code></label>
                                            <textarea
                                                class="form-control @error('merchant_signing_private_key') is-invalid @enderror merchant_signing_private_key"
                                                name="merchant_signing_private_key" id="merchant_signing_private_key" rows="8">{{ $internationalPayment ? $internationalPayment->merchant_signing_private_key : '' }}</textarea>

                                            <div class="text-danger" id="merchantSigningPrivateKeyError"></div>
                                            @error('merchant_signing_private_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label"
                                                for="paco_encryption_public_key">PacoEncryptionPublicKey
                                                <code>*</code></label>
                                            <textarea class="form-control @error('paco_encryption_public_key') is-invalid @enderror paco_encryption_public_key"
                                                name="paco_encryption_public_key" id="paco_encryption_public_key" rows="8">{{ $internationalPayment ? $internationalPayment->paco_encryption_public_key : '' }}</textarea>
                                            <div class="text-danger" id="pacoEncryptionPublicKeyError"></div>
                                            @error('paco_encryption_public_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label"
                                                for="merchant_decryption_private_key">MerchantDecryptionPrivateKey
                                                <code>*</code></label>
                                            <textarea
                                                class="form-control @error('merchant_decryption_private_key') is-invalid @enderror merchant_decryption_private_key"
                                                name="merchant_decryption_private_key" id="merchant_decryption_private_key" rows="8">{{ $internationalPayment ? $internationalPayment->merchant_decryption_private_key : '' }}</textarea>
                                            <div class="text-danger" id="merchantDecryptionPrivateKeyError"></div>
                                            @error('merchant_decryption_private_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label" for="paco_signing_public_key">PacoSigningPublicKey
                                                <code>*</code></label>
                                            <textarea class="form-control @error('paco_signing_public_key') is-invalid @enderror paco_signing_public_key"
                                                name="paco_signing_public_key" id="paco_signing_public_key" rows="8">{{ $internationalPayment ? $internationalPayment->paco_signing_public_key : '' }}</textarea>
                                            <div class="text-danger" id="pacoSigningPublicKeyError"></div>
                                            @error('paco_signing_public_key')
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>


                                        <div class="col-md-12" style="display: flex; justify-content: end;">
                                            <button type="submit" class="btn btn-primary submitData"
                                                id="submitData">{{ $internationalPayment ? 'Update' : 'Save' }}</button>
                                        </div>
                                    </div>

                                </div>
                                <div class="tab-pane fade" id="profilePIll" role="tabpanel"
                                    aria-labelledby="profile-icon-pill">
                                    Etsy mixtape wayfarers, ethical wes anderson tofu before they sold out mcsweeney's
                                    organic
                                    lomo
                                    retro fanny pack lo-fi farm-to-table readymade. Messenger bag gentrify pitchfork
                                    tattooed
                                    craft
                                    beer, iphone skateboard locavore.
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("#openModal").modal('show');

            // Update hidden inputs when tabs change
            $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).attr('href');

                // National tabs
                if (target === '#fonePIll') {
                    $('#currentNationalTab').val('fonepay');
                } else if (target === '#mocoPIll') {
                    $('#currentNationalTab').val('moco');
                } else if (target === '#esewaPIll') {
                    $('#currentNationalTab').val('esewa');
                } else if (target === '#khaltiPIll') {
                    $('#currentNationalTab').val('khalti');
                } else if (target === '#bankPIll') {
                    $('#currentNationalTab').val('bank');
                }

                // International tabs
                else if (target === '#homePIll') {
                    $('#currentInternationalTab').val('himalayan_bank');
                } else if (target === '#profilePIll') {
                    $('#currentInternationalTab').val('account_details');
                }

                console.log('Tab changed - National:', $('#currentNationalTab').val(), 'International:', $(
                    '#currentInternationalTab').val());
            });

            $(".numericValue").on("keydown", function(event) {
                // Allow backspace, delete, tab, escape, and enter keys
                if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode ==
                    27 || event.keyCode == 13 ||
                    // Allow Ctrl+A
                    (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Allow home, end, left, right
                    (event.keyCode >= 35 && event.keyCode <= 39) ||
                    // Allow numbers from the main keyboard (0-9) and the numpad (96-105)
                    (event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <=
                        105)) {
                    return;
                } else {
                    event.preventDefault();
                }
            });

            $('input[name="payment"]').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue == 'national') {
                    $('#nationalSection').show();
                    $('#internationalSection').hide();
                }
                if (selectedValue == 'international') {
                    $('#nationalSection').hide();
                    $('#internationalSection').show();
                }
            });

            $('.submitData').click(function(event) {
                event.preventDefault();

                let isValid = true;
                let activeSection = $('#nationalSection').is(':visible') ? 'national' : 'international';
                let activeTab = '';

                $('.text-danger').text('');

                // Get active tab from hidden inputs
                if (activeSection === 'national') {
                    activeTab = $('#currentNationalTab').val();
                } else if (activeSection === 'international') {
                    activeTab = $('#currentInternationalTab').val();
                }

                console.log('Submitting - Section:', activeSection, 'Tab:', activeTab);

                if (activeSection === 'national') {
                    if (activeTab === 'fonepay') {
                        let profileId = $('#profile_id').val().trim();
                        let secretKey = $('#secret_key').val().trim();

                        if (!profileId) {
                            isValid = false;
                            $('#profileIdError').text('Profile Id is required.');
                        }
                        if (!secretKey) {
                            isValid = false;
                            $('#secretKeyError').text('Shared Secret Key is required.');
                        }
                    } else if (activeTab === 'moco') {
                        let mocoMerchantId = $('#moco_merchant_id').val().trim();
                        let mocoOutletId = $('#moco_outlet_id').val().trim();
                        let mocoTerminalId = $('#moco_terminal_id').val().trim();
                        let mocoSharedKey = $('#moco_shared_key').val().trim();

                        if (!mocoMerchantId) {
                            isValid = false;
                            $('#mocoMerchantIdError').text('Merchant Id is required.');
                        }
                        if (!mocoOutletId) {
                            isValid = false;
                            $('#mocoOutletIdError').text('Outlet Id is required.');
                        }
                        if (!mocoTerminalId) {
                            isValid = false;
                            $('#mocoTerminalIdError').text('Terminal Id is required.');
                        }
                        if (!mocoSharedKey) {
                            isValid = false;
                            $('#mocoSharedKeyError').text('Shared Key is required.');
                        }
                    } else if (activeTab === 'esewa') {
                        let esewaProductCodeId = $('#esewa_product_code').val().trim();
                        let esewaSecretKeyId = $('#esewa_secret_key').val().trim();

                        if (!esewaProductCodeId) {
                            isValid = false;
                            $('#productCodeError').text('Product Code is required.');
                        }
                        if (!esewaSecretKeyId) {
                            isValid = false;
                            $('#esewaSecretKeyError').text('Secret Key is required.');
                        }
                    } else if (activeTab === 'khalti') {
                        let khaltiLiveSecretKeyId = $('#khalti_live_secret_key').val().trim();

                        if (!khaltiLiveSecretKeyId) {
                            isValid = false;
                            $('#khaltiLiveSecretKeyError').text('Live Secret Key is required.');
                        }
                    }
                }

                if (activeSection === 'international') {
                    if (activeTab === 'himalayan_bank') {
                        let merchantKey = $('#merchant_key').val().trim();
                        let apiKey = $('#api_key').val().trim();
                        let accessToken = $('#access_token').val().trim();
                        let merchantSigningPrivateKey = $('#merchant_signing_private_key').val().trim();
                        let pacoEncryptionPublicKey = $('#paco_encryption_public_key').val().trim();
                        let merchantDecryptionPrivateKey = $('#merchant_decryption_private_key').val()
                            .trim();
                        let pacoSigningPublicKey = $('#paco_signing_public_key').val().trim();

                        if (!merchantKey) {
                            isValid = false;
                            $('#merchantKeyError').text('Merchant Key is required.');
                        }
                        if (!apiKey) {
                            isValid = false;
                            $('#apiKeyError').text('API Key is required.');
                        }
                        if (!accessToken) {
                            isValid = false;
                            $('#accessTokenError').text('AccessToken is required.');
                        }
                        if (!merchantSigningPrivateKey) {
                            isValid = false;
                            $('#merchantSigningPrivateKeyError').text(
                                'MerchantSigningPrivateKey is required.');
                        }
                        if (!pacoEncryptionPublicKey) {
                            isValid = false;
                            $('#pacoEncryptionPublicKeyError').text('PacoEncryptionPublicKey is required.');
                        }
                        if (!merchantDecryptionPrivateKey) {
                            isValid = false;
                            $('#merchantDecryptionPrivateKeyError').text(
                                'MerchantDecryptionPrivateKey is required.');
                        }
                        if (!pacoSigningPublicKey) {
                            isValid = false;
                            $('#pacoSigningPublicKeyError').text('PacoSigningPublicKey is required.');
                        }
                    }
                }

                if (isValid) {
                    let formData = {
                        section: activeSection,
                        active_tab: activeTab,
                        // FonePay fields
                        profile_id: $('#profile_id').val(),
                        secret_key: $('#secret_key').val(),
                        // Moco fields
                        moco_merchant_id: $('#moco_merchant_id').val(),
                        moco_outlet_id: $('#moco_outlet_id').val(),
                        moco_terminal_id: $('#moco_terminal_id').val(),
                        moco_shared_key: $('#moco_shared_key').val(),
                        //Esewa Field
                        esewa_product_code: $('#esewa_product_code').val(),
                        esewa_secret_key: $('#esewa_secret_key').val(),
                        //Khalti Field
                        khalti_live_secret_key: $('#khalti_live_secret_key').val(),
                        // Himalayan Bank fields 
                        merchant_key: $('#merchant_key').val(),
                        api_key: $('#api_key').val(),
                        access_token: $('#access_token').val(),
                        merchant_signing_private_key: $('#merchant_signing_private_key').val(),
                        paco_encryption_public_key: $('#paco_encryption_public_key').val(),
                        merchant_decryption_private_key: $('#merchant_decryption_private_key').val(),
                        paco_signing_public_key: $('#paco_signing_public_key').val(),
                        // IDs for updates
                        id: $('#id').val(),
                        international_id: $('#international_id').val(),
                    };

                    console.log('Form data being sent:', formData);

                    $.ajax({
                        url: '{{ route('payment.setting.submit', $society) }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            notyf.success(response.message);
                        },
                        error: function(error) {
                            console.log('Error:', error);
                            if (error.responseJSON && error.responseJSON.errors) {
                                // Handle validation errors
                                $.each(error.responseJSON.errors, function(key, value) {
                                    console.log('Validation error:', key, value);
                                });
                            }
                            notyf.error('There was an error submitting the form.');
                        },
                    });
                }
            });
        });
    </script>
@endsection
