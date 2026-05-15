@extends('layouts.guest')

@section('title', 'Create Dispatcher Account')

@section('content')
<div class="register-page">
    <div class="register-container">

        {{-- Header --}}
        <div class="register-header">
            <div class="register-brand">
                <div class="register-brand-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 3h15v11H1zM16 8h4l3 3v5h-7V8z"/>
                        <circle cx="5.5" cy="17.5" r="2.5"/>
                        <circle cx="18.5" cy="17.5" r="2.5"/>
                    </svg>
                </div>
                <span class="register-brand-name">Cup's Dispatch</span>
            </div>
            <h1>New Dispatcher</h1>
            <p>Register a new dispatcher account</p>
        </div>

        {{-- Server-side errors --}}
        @if ($errors->any())
            <div class="register-error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
            @csrf
            <input type="hidden" name="role" value="dispatcher">

            {{-- ── Personal Information ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <h3>Personal Information</h3>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="surname">Surname <span class="required">*</span></label>
                        <input id="surname" type="text" name="surname"
                               value="{{ old('surname') }}" placeholder="Dela Cruz">
                        @error('surname')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input id="first_name" type="text" name="first_name"
                               value="{{ old('first_name') }}" placeholder="Juan">
                        @error('first_name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="middle_name">Middle Name</label>
                        <input id="middle_name" type="text" name="middle_name"
                               value="{{ old('middle_name') }}" placeholder="Santos">
                    </div>

                    <div class="form-field">
                        <label for="suffix">Suffix</label>
                        <select id="suffix" name="suffix">
                            <option value="">—</option>
                            <option value="Jr"  @selected(old('suffix') === 'Jr')>Jr.</option>
                            <option value="Sr"  @selected(old('suffix') === 'Sr')>Sr.</option>
                            <option value="II"  @selected(old('suffix') === 'II')>II</option>
                            <option value="III" @selected(old('suffix') === 'III')>III</option>
                        </select>
                    </div>

                    <div class="form-field">
                        <label for="birth_date">Birth Date <span class="required">*</span></label>
                        <input id="birth_date" type="date" name="birth_date"
                               value="{{ old('birth_date') }}"
                               onchange="calcAge(this.value)">
                        @error('birth_date')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="age_display">Age</label>
                        <input id="age_display" type="text" disabled readonly placeholder="—">
                    </div>

                    <div class="form-field">
                        <label for="sex">Sex <span class="required">*</span></label>
                        <select id="sex" name="sex">
                            <option value="">Select</option>
                            <option value="male"   @selected(old('sex') === 'male')>Male</option>
                            <option value="female" @selected(old('sex') === 'female')>Female</option>
                            <option value="other"  @selected(old('sex') === 'other')>Other</option>
                        </select>
                        @error('sex')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="marital_status">Marital Status <span class="required">*</span></label>
                        <select id="marital_status" name="marital_status">
                            <option value="">Select</option>
                            <option value="single"   @selected(old('marital_status') === 'single')>Single</option>
                            <option value="married"  @selected(old('marital_status') === 'married')>Married</option>
                            <option value="divorced" @selected(old('marital_status') === 'divorced')>Divorced</option>
                            <option value="widowed"  @selected(old('marital_status') === 'widowed')>Widowed</option>
                        </select>
                        @error('marital_status')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- ── Contact Information ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <h3>Contact Information</h3>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="phone_number">Phone <span class="required">*</span></label>
                        <div class="phone-group">
                            <span class="phone-prefix">+63</span>
                            <input id="phone_number" type="tel" name="phone_number"
                                   value="{{ ltrim(old('phone_number', ''), '+63') }}"
                                   placeholder="9123456789"
                                   maxlength="10"
                                   onkeypress="return /[0-9]/.test(event.key)"
                                   oninput="this.value = this.value.replace(/\D/g,'').slice(0,10)">
                        </div>
                        <span class="hint">10-digit number (no spaces or dashes)</span>
                        @error('phone_number')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="address">Address <span class="required">*</span></label>
                        <input id="address" type="text" name="address"
                               value="{{ old('address') }}"
                               placeholder="Unit/House No., Street, Barangay, City, Province, ZIP">
                        @error('address')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Emergency Contact --}}
                <div style="margin-top: 1.25rem;">
                    <div class="form-card-header" style="margin-bottom: 1rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <h3>Emergency Contact</h3>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="emergency_contact_name">Name <span class="required">*</span></label>
                            <input id="emergency_contact_name" type="text"
                                   name="emergency_contact_name"
                                   value="{{ old('emergency_contact_name') }}"
                                   placeholder="Full name">
                            @error('emergency_contact_name')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-field">
                            <label for="emergency_contact_relationship">Relationship <span class="required">*</span></label>
                            <select id="emergency_contact_relationship" name="emergency_contact_relationship">
                                <option value="">Select</option>
                                <option value="Mother"  @selected(old('emergency_contact_relationship') === 'Mother')>Mother</option>
                                <option value="Father"  @selected(old('emergency_contact_relationship') === 'Father')>Father</option>
                                <option value="Spouse"  @selected(old('emergency_contact_relationship') === 'Spouse')>Spouse</option>
                                <option value="Sibling" @selected(old('emergency_contact_relationship') === 'Sibling')>Sibling</option>
                                <option value="Other"   @selected(old('emergency_contact_relationship') === 'Other')>Other</option>
                            </select>
                            @error('emergency_contact_relationship')<span class="field-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-field">
                            <label for="emergency_contact_number">Phone <span class="required">*</span></label>
                            <div class="phone-group">
                                <span class="phone-prefix">+63</span>
                                <input id="emergency_contact_number" type="tel"
                                       name="emergency_contact_number"
                                       value="{{ ltrim(old('emergency_contact_number', ''), '+63') }}"
                                       placeholder="9123456789"
                                       maxlength="10"
                                       onkeypress="return /[0-9]/.test(event.key)"
                                       oninput="this.value = this.value.replace(/\D/g,'').slice(0,10)">
                            </div>
                            <span class="hint">10-digit number (no spaces or dashes)</span>
                            @error('emergency_contact_number')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Documents (Optional) ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <h3>Documents <span class="optional">(Optional)</span></h3>
                </div>

                <div class="docs-grid">
                    @foreach ([
                        ['name' => 'health_card',      'label' => 'Health Card',      'preview' => 'preview_health_card'],
                        ['name' => 'nbi_clearance',    'label' => 'NBI Clearance',    'preview' => 'preview_nbi_clearance'],
                        ['name' => 'police_clearance', 'label' => 'Police Clearance', 'preview' => 'preview_police_clearance'],
                    ] as $doc)
                    <div class="doc-field">
                        <label>{{ $doc['label'] }}</label>
                        <div class="file-zone" onclick="document.getElementById('file_{{ $doc['name'] }}').click()">
                            <input id="file_{{ $doc['name'] }}" type="file"
                                   name="{{ $doc['name'] }}"
                                   accept="image/jpeg,image/png,application/pdf"
                                   style="display:none"
                                   onchange="previewFile(this, '{{ $doc['preview'] }}')">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <span>Upload</span>
                        </div>
                        <div id="{{ $doc['preview'] }}" class="file-preview"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Account Access ── --}}
            <div class="form-card">
                <div class="form-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M16 9a4 4 0 0 0-8 0"/>
                    </svg>
                    <h3>Account Access</h3>
                </div>

                <div class="form-grid">
                    <div class="form-field">
                        <label for="email">Email <span class="required">*</span></label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}"
                               placeholder="name@company.com"
                               autocomplete="email">
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="password">Password <span class="required">*</span></label>
                        <input id="password" type="password" name="password"
                               placeholder="8+ characters"
                               autocomplete="new-password">
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-field">
                        <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               placeholder="Repeat password"
                               autocomplete="new-password">
                        @error('password_confirmation')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            {{-- ── Actions ── --}}
            <div class="form-actions">
                <div class="action-group">
                    <a href="{{ route('dashboard') }}" class="btn-secondary">Cancel</a>
                    <button type="button" class="btn-primary" onclick="showRegistrationModal()">
                        Create Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Include the custom register modal component --}}
<x-register-modal />

<script>
    // Show a single client-side error message
    function showClientError(message) {
        let errorDiv = document.querySelector('.register-error');
        if (!errorDiv) {
            const form = document.getElementById('registerForm');
            errorDiv = document.createElement('div');
            errorDiv.className = 'register-error';
            form.insertBefore(errorDiv, form.firstChild);
        }
        errorDiv.innerHTML = `<div>${message}</div>`;
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearClientErrors() {
        const errorDiv = document.querySelector('.register-error');
        if (errorDiv) errorDiv.innerHTML = '';
    }

    // Validate only required fields (generic) + password match (specific)
    function validateForm() {
        clearClientErrors();

        // Helper to check if a field is empty
        const isEmpty = (val) => val === null || val.trim() === '';

        // Gather all required field values
        const surname = document.getElementById('surname').value;
        const firstName = document.getElementById('first_name').value;
        const birthDate = document.getElementById('birth_date').value;
        const sex = document.getElementById('sex').value;
        const maritalStatus = document.getElementById('marital_status').value;
        const phoneRaw = document.getElementById('phone_number').value;
        const address = document.getElementById('address').value;
        const emergencyName = document.getElementById('emergency_contact_name').value;
        const emergencyRelationship = document.getElementById('emergency_contact_relationship').value;
        const emergencyPhoneRaw = document.getElementById('emergency_contact_number').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        // Check each required field (including format validations as part of required)
        let hasRequiredError = false;

        // Required text / date / select fields
        if (isEmpty(surname)) hasRequiredError = true;
        else if (isEmpty(firstName)) hasRequiredError = true;
        else if (isEmpty(birthDate)) hasRequiredError = true;
        else if (isEmpty(sex)) hasRequiredError = true;
        else if (isEmpty(maritalStatus)) hasRequiredError = true;
        else if (isEmpty(phoneRaw)) hasRequiredError = true;
        else if (isEmpty(address)) hasRequiredError = true;
        else if (isEmpty(emergencyName)) hasRequiredError = true;
        else if (isEmpty(emergencyRelationship)) hasRequiredError = true;
        else if (isEmpty(emergencyPhoneRaw)) hasRequiredError = true;
        else if (isEmpty(email)) hasRequiredError = true;
        else if (isEmpty(password)) hasRequiredError = true;
        else if (isEmpty(confirmPassword)) hasRequiredError = true;

        // Format validations (still result in generic message)
        if (!hasRequiredError) {
            const phoneDigits = phoneRaw.replace(/\D/g, '');
            if (phoneDigits.length !== 10) hasRequiredError = true;
            const emergencyDigits = emergencyPhoneRaw.replace(/\D/g, '');
            if (emergencyDigits.length !== 10) hasRequiredError = true;
            if (!/^\S+@\S+\.\S+$/.test(email)) hasRequiredError = true;
            if (password.length < 8) hasRequiredError = true;

            // Age validation (18+)
            if (birthDate) {
                const age = calculateAgeFromDate(birthDate);
                if (age === null || age < 18) hasRequiredError = true;
            }
        }

        // Specific password match check
        const passwordMatch = (password === confirmPassword);

        if (!passwordMatch) {
            showClientError('Passwords do not match.');
            return false;
        }

        if (hasRequiredError) {
            showClientError('Please fill the required fields indicated with (*).');
            return false;
        }

        return true;
    }

    function calculateAgeFromDate(dateStr) {
        if (!dateStr) return null;
        const birth = new Date(dateStr);
        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        return (age >= 0 && age < 120) ? age : null;
    }

    // Show modal only after validation passes
    function showRegistrationModal() {
        if (!validateForm()) {
            return;
        }
        window.registerModal.open(function() {
            prepareAndSubmit();
        });
    }

    function prepareAndSubmit() {
        var phone = document.getElementById('phone_number');
        var ePhone = document.getElementById('emergency_contact_number');

        if (phone && phone.value && !phone.value.startsWith('+63')) {
            phone.value = '+63' + phone.value;
        }
        if (ePhone && ePhone.value && !ePhone.value.startsWith('+63')) {
            ePhone.value = '+63' + ePhone.value;
        }

        document.getElementById('registerForm').submit();
    }

    // Auto-calculate age from birth date (display only)
    function calcAge(dateStr) {
        var el = document.getElementById('age_display');
        if (!dateStr) { el.value = ''; return; }
        var birth = new Date(dateStr);
        var today = new Date();
        var age = today.getFullYear() - birth.getFullYear();
        var m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        el.value = (age > 0 && age < 120) ? age + ' yrs' : '';
    }

    // File preview
    function previewFile(input, previewId) {
        var preview = document.getElementById(previewId);
        if (!preview) return;
        var file = input.files[0];
        if (!file) { preview.innerHTML = ''; return; }
        if (file.size > 5 * 1024 * 1024) {
            alert('File must be under 5 MB');
            input.value = '';
            preview.innerHTML = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = file.type.startsWith('image/')
                ? '<img src="' + e.target.result + '" alt="Preview">'
                : '<div class="file-name">📄 ' + file.name + '</div>';
        };
        reader.readAsDataURL(file);
    }

    // Initial age calculation if old birth date exists
    (function() {
        var bd = document.getElementById('birth_date');
        if (bd && bd.value) calcAge(bd.value);
    })();
</script>
@endsection
