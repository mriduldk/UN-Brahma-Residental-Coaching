@extends('layouts.student')

@section('content')
<x-missing-session-popup />

<div class="w-full space-y-6">
    <!-- Header -->
    <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Edit Profile</h2>
            <p class="text-slate-500 text-sm">Update your personal and academic information.</p>
        </div>
        <a href="{{ route('student.profile') }}" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg flex items-center">
            <i data-lucide="arrow-left" class="mr-2" size="18"></i> Back to Profile
        </a>
    </div>

    <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-6" id="profileUpdateForm">
        @csrf
        
        <!-- Personal Info -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="user" class="mr-2 text-blue-600" size="20"></i> Personal Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name</label><input type="text" name="full_name" value="{{ $student->full_name }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phone Number</label><input type="text" name="phone_number" value="{{ $student->phone_number }}" class="w-full p-2 border border-slate-300 rounded"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email (Optional)</label><input type="email" name="email" value="{{ $student->email }}" class="w-full p-2 border border-slate-300 rounded"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Date of Birth</label><input type="date" name="dob" value="{{ $student->dob }}" class="w-full p-2 border border-slate-300 rounded"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gender</label>
                    <select name="gender" class="w-full p-2 border border-slate-300 rounded">
                        <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $student->gender == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full p-2 border border-slate-300 rounded">
                        <option value="">Select</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                        <option value="{{ $bg }}" {{ $student->blood_group == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Category</label>
                    <select name="category" class="w-full p-2 border border-slate-300 rounded">
                        <option value="General" {{ $student->category == 'General' ? 'selected' : '' }}>General</option>
                        <option value="SC" {{ $student->category == 'SC' ? 'selected' : '' }}>SC</option>
                        <option value="ST" {{ $student->category == 'ST' ? 'selected' : '' }}>ST</option>
                        <option value="OBC" {{ $student->category == 'OBC' ? 'selected' : '' }}>OBC</option>
                        <option value="MOBC" {{ $student->category == 'MOBC' ? 'selected' : '' }}>MOBC</option>
                    </select>
                </div>
                
                <div class="col-span-1 md:col-span-2 pt-2">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" name="is_pwd" value="1" {{ $student->is_pwd ? 'checked' : '' }} class="w-5 h-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500 transition">
                        <div>
                            <span class="text-sm font-bold text-slate-700">Are you a Person with Benchmark Disability (PwD)?</span>
                        </div>
                    </label>
                </div>

                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Religion</label><input type="text" name="religion" value="{{ $student->religion }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nationality</label><input type="text" name="nationality" value="{{ $student->nationality ?? 'Indian' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mother Tongue</label><input type="text" name="mother_tongue" value="{{ $student->mother_tongue }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Marital Status</label>
                    <select name="marital_status" class="w-full p-2 border border-slate-300 rounded">
                        <option value="Unmarried" {{ $student->marital_status == 'Unmarried' ? 'selected' : '' }}>Unmarried</option>
                        <option value="Married" {{ $student->marital_status == 'Married' ? 'selected' : '' }}>Married</option>
                        <option value="Divorced" {{ $student->marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                    </select>
                </div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Identification Mark</label><input type="text" name="identification_mark" value="{{ $student->identification_mark }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                <div class="col-span-1 md:col-span-2"><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Employment Exchange Registration Number <span class="text-xs font-normal lowercase">(Optional)</span></label><input type="text" name="employment_exchange_no" value="{{ $student->employment_exchange_no }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Permanent Resident of Assam <span class="text-red-500">*</span></label>
                    <select name="is_permanent_resident_of_assam" required class="w-full p-2 border border-slate-300 rounded">
                        <option value="">Select Yes/No</option>
                        <option value="1" {{ $student->is_permanent_resident_of_assam === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $student->is_permanent_resident_of_assam === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">BTC Resident <span class="text-red-500">*</span></label>
                    <select name="is_btc_resident" required class="w-full p-2 border border-slate-300 rounded">
                        <option value="">Select Yes/No</option>
                        <option value="1" {{ $student->is_btc_resident === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $student->is_btc_resident === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ex-Serviceman <span class="text-red-500">*</span></label>
                    <select name="is_ex_serviceman" required class="w-full p-2 border border-slate-300 rounded">
                        <option value="">Select Yes/No</option>
                        <option value="1" {{ $student->is_ex_serviceman === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $student->is_ex_serviceman === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">NCC Certificate <span class="text-red-500">*</span></label>
                    <select name="ncc_certificate" required class="w-full p-2 border border-slate-300 rounded">
                        <option value="None" {{ strcasecmp($student->ncc_certificate ?? '', 'None') === 0 || empty($student->ncc_certificate) ? 'selected' : '' }}>None</option>
                        <option value="B Certificate" {{ $student->ncc_certificate === 'B Certificate' ? 'selected' : '' }}>B Certificate</option>
                        <option value="C Certificate" {{ $student->ncc_certificate === 'C Certificate' ? 'selected' : '' }}>C Certificate</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">National Sports Achievement <span class="text-red-500">*</span></label>
                    <select name="has_national_sports_achievement" required class="w-full p-2 border border-slate-300 rounded">
                        <option value="">Select Yes/No</option>
                        <option value="1" {{ $student->has_national_sports_achievement === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $student->has_national_sports_achievement === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Fine Arts/Cultural Achievement <span class="text-red-500">*</span></label>
                    <select name="has_fine_arts_cultural_achievement" required class="w-full p-2 border border-slate-300 rounded">
                        <option value="">Select Yes/No</option>
                        <option value="1" {{ $student->has_fine_arts_cultural_achievement === true ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ $student->has_fine_arts_cultural_achievement === false ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Alternate Mobile</label><input type="text" name="alternate_mobile_number" value="{{ $student->alternate_mobile_number }}" class="w-full p-2 border border-slate-300 rounded"></div>
            </div>
        </div>

        <!-- Address Info -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="map-pin" class="mr-2 text-blue-600" size="20"></i> Address Details
            </h3>
            
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                 <!-- Present -->
                 <div class="space-y-4">
                    <h4 class="font-bold text-slate-600 bg-slate-50 p-2 rounded">Present Address</h4>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vill/Town</label><input type="text" name="present_village_town" value="{{ $student->address->present_village_town ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">District</label><input type="text" name="present_district" value="{{ $student->address->present_district ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">State</label><input type="text" name="present_state" value="{{ $student->address->present_state ?? 'ASSAM' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Country</label><input type="text" name="present_country" value="{{ $student->address->present_country ?? 'India' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">PIN</label><input type="text" name="present_pin_code" value="{{ $student->address->present_pin_code ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                 </div>

                 <!-- Permanent -->
                 <div class="space-y-4">
                    <div class="flex items-center justify-between bg-slate-50 p-2 rounded">
                        <h4 class="font-bold text-slate-600">Permanent Address</h4>
                        <div class="flex items-center">
                            <input type="checkbox" id="sameAsPresent" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            <label for="sameAsPresent" class="ml-2 text-xs font-bold text-slate-500 uppercase cursor-pointer select-none">Same as Present</label>
                        </div>
                    </div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vill/Town</label><input type="text" name="permanent_village_town" value="{{ $student->address->permanent_village_town ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">District</label><input type="text" name="permanent_district" value="{{ $student->address->permanent_district ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">State</label><input type="text" name="permanent_state" value="{{ $student->address->permanent_state ?? 'ASSAM' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Country</label><input type="text" name="permanent_country" value="{{ $student->address->permanent_country ?? 'India' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">PIN</label><input type="text" name="permanent_pin_code" value="{{ $student->address->permanent_pin_code ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                 </div>
             </div>
        </div>

        <!-- Parents Info -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="users" class="mr-2 text-blue-600" size="20"></i> Parent / Guardian Details
            </h3>
            
            <div class="space-y-6">
                <!-- Father -->
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <h4 class="font-bold text-slate-600 mb-3 border-b border-slate-200 pb-2">Father's Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Name</label><input type="text" name="father_name" value="{{ $student->parent->father_name ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mobile</label><input type="text" name="father_mobile" value="{{ $student->parent->father_mobile ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label><input type="text" name="father_email" value="{{ $student->parent->father_email ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Occupation</label><input type="text" name="father_occupation" value="{{ $student->parent->father_occupation ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Annual Income</label><input type="number" name="father_annual_income" value="{{ $student->parent->father_annual_income ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Qualification</label><input type="text" name="father_qualification" value="{{ $student->parent->father_qualification ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    </div>
                </div>

                <!-- Mother -->
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <h4 class="font-bold text-slate-600 mb-3 border-b border-slate-200 pb-2">Mother's Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Name</label><input type="text" name="mother_name" value="{{ $student->parent->mother_name ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mobile</label><input type="text" name="mother_mobile" value="{{ $student->parent->mother_mobile ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label><input type="text" name="mother_email" value="{{ $student->parent->mother_email ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Occupation</label><input type="text" name="mother_occupation" value="{{ $student->parent->mother_occupation ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Annual Income</label><input type="number" name="mother_annual_income" value="{{ $student->parent->mother_annual_income ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Qualification</label><input type="text" name="mother_qualification" value="{{ $student->parent->mother_qualification ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    </div>
                </div>

                <!-- Guardian -->
                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <h4 class="font-bold text-slate-600 mb-3 border-b border-slate-200 pb-2">Guardian's Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Name</label><input type="text" name="guardian_name" value="{{ $student->parent->guardian_name ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Relation</label><input type="text" name="guardian_relation" value="{{ $student->parent->guardian_relation ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mobile</label><input type="text" name="guardian_mobile" value="{{ $student->parent->guardian_mobile ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                        <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Occupation</label><input type="text" name="guardian_occupation" value="{{ $student->parent->guardian_occupation ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                        <div class="md:col-span-2"><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Address</label><input type="text" name="guardian_address" value="{{ $student->parent->guardian_address ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Details -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="landmark" class="mr-2 text-blue-600" size="20"></i> Bank Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Account Holder Name</label><input type="text" name="account_holder_name" value="{{ $student->bank_detail->account_holder_name ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                 <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Bank Name</label><input type="text" name="bank_name" value="{{ $student->bank_detail->bank_name ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                 <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Branch</label><input type="text" name="branch" value="{{ $student->bank_detail->branch ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                 <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">Account Number</label><input type="text" name="account_number" value="{{ $student->bank_detail->account_number ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
                 <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">IFSC Code</label><input type="text" name="ifsc_code" value="{{ $student->bank_detail->ifsc_code ?? '' }}" class="w-full p-2 border border-slate-300 rounded uppercase"></div>
                 <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1">UPI ID</label><input type="text" name="upi_id" value="{{ $student->bank_detail->upi_id ?? '' }}" class="w-full p-2 border border-slate-300 rounded"></div>
            </div>
        </div>

        <!-- Academic Details -->
        <div class="bg-white shadow-sm border border-slate-200 rounded-xl p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3 flex items-center">
                <i data-lucide="graduation-cap" class="mr-2 text-blue-600" size="20"></i> Qualification Details
            </h3>
            <div class="overflow-x-auto border border-slate-200 rounded-lg">
                <table class="w-full text-sm text-left" id="qualTable">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 min-w-[150px]">Exam</th>
                            <th class="px-4 py-3 min-w-[80px]">Year</th>
                            <th class="px-4 py-3 min-w-[150px]">Board / University</th>
                            <th class="px-4 py-3 min-w-[120px]">Roll No</th>
                            <th class="px-4 py-3 min-w-[120px]">Reg No</th>
                            <th class="px-4 py-3 min-w-[100px]">Medium</th>
                            <th class="px-4 py-3 min-w-[80px]">Total</th>
                            <th class="px-4 py-3 min-w-[80px]">Obtained</th>
                            <th class="px-4 py-3 min-w-[80px]">%</th>
                            <th class="px-4 py-3 min-w-[80px]">Div</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="qualBody">
                        @foreach($student->academic_qualifications as $index => $qual)
                        <tr>
                            <td class="p-2"><input type="text" name="qual[{{ $index }}][exam]" value="{{ $qual->examination }}" class="w-full p-2 border rounded uppercase exam-tom-select" placeholder="Exam"></td>
                            <td class="p-2"><input type="number" name="qual[{{ $index }}][year]" value="{{ $qual->passing_year }}" class="w-full p-2 border rounded" placeholder="Year"></td>
                            <td class="p-2"><input type="text" name="qual[{{ $index }}][board]" value="{{ $qual->board_university }}" class="w-full p-2 border rounded uppercase" placeholder="Board"></td>
                            <td class="p-2"><input type="text" name="qual[{{ $index }}][roll_no]" value="{{ $qual->roll_number }}" class="w-full p-2 border rounded uppercase" placeholder="Roll No"></td>
                            <td class="p-2"><input type="text" name="qual[{{ $index }}][reg_no]" value="{{ $qual->registration_number }}" class="w-full p-2 border rounded uppercase" placeholder="Reg No"></td>
                            <td class="p-2"><input type="text" name="qual[{{ $index }}][medium]" value="{{ $qual->medium_of_study }}" class="w-full p-2 border rounded uppercase" placeholder="Medium"></td>
                            <td class="p-2"><input type="number" name="qual[{{ $index }}][total_marks]" value="{{ $qual->total_marks }}" class="w-full p-2 border rounded" placeholder="Total"></td>
                            <td class="p-2"><input type="number" name="qual[{{ $index }}][marks_obtained]" value="{{ $qual->marks_obtained }}" class="w-full p-2 border rounded" placeholder="Obt"></td>
                            <td class="p-2"><input type="text" name="qual[{{ $index }}][marks]" value="{{ $qual->percentage_grade }}" class="w-full p-2 border rounded" placeholder="%"></td>
                            <td class="p-2"><input type="text" name="qual[{{ $index }}][div]" value="{{ $qual->division }}" class="w-full p-2 border rounded uppercase" placeholder="Div"></td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700"><i data-lucide="trash-2" size="18"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="addRow()" class="mt-2 flex items-center space-x-2 text-sm text-blue-600 font-medium hover:text-blue-800">
                <i data-lucide="plus" size="16"></i> <span>Add Qualification</span>
            </button>
        </div>

        <div class="flex justify-end pt-6">
            <button type="submit" id="saveBtn" class="px-8 py-3 bg-blue-600 text-white font-bold rounded-lg shadow hover:bg-blue-700 flex items-center disabled:opacity-75 disabled:cursor-not-allowed">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" id="saveSpinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <i data-lucide="save" id="saveBtnIcon" class="mr-2" size="20"></i> 
                <span id="saveBtnText">Save Changes</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    function initTomSelectExam(element) {
        if (element.tomselect) return;
        new TomSelect(element, {
            create: false,
            maxItems: 1,
            dropdownParent: 'body',
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            options: [
                {value: 'HSLC', text: 'HSLC'},
                {value: 'HS', text: 'HS'},
                {value: 'GRADUATION', text: 'Graduation'},
                {value: 'POST GRADUATION', text: 'Post Graduation'},
                {value: 'B.ED.', text: 'B.Ed.'},
                {value: 'PARANGAT', text: 'Parangat'},
                {value: 'OTHER', text: 'Other'}
            ],
            render: {
                option_create: function(data, escape) {
                    return '<div class="create">Add <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.exam-tom-select').forEach(el => initTomSelectExam(el));
    });

    let rowCount = {{ $student->academic_qualifications->count() > 0 ? $student->academic_qualifications->count() : 0 }};
    function addRow() {
        const tbody = document.getElementById('qualBody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="p-2"><input type="text" name="qual[${rowCount}][exam]" class="w-full p-2 border rounded uppercase exam-tom-select" placeholder="Exam"></td>
            <td class="p-2"><input type="number" name="qual[${rowCount}][year]" class="w-full p-2 border rounded" placeholder="Year"></td>
            <td class="p-2"><input type="text" name="qual[${rowCount}][board]" class="w-full p-2 border rounded uppercase" placeholder="Board"></td>
            <td class="p-2"><input type="text" name="qual[${rowCount}][roll_no]" class="w-full p-2 border rounded uppercase" placeholder="Roll No"></td>
            <td class="p-2"><input type="text" name="qual[${rowCount}][reg_no]" class="w-full p-2 border rounded uppercase" placeholder="Reg No"></td>
            <td class="p-2"><input type="text" name="qual[${rowCount}][medium]" class="w-full p-2 border rounded uppercase" placeholder="Medium"></td>
            <td class="p-2"><input type="number" name="qual[${rowCount}][total_marks]" class="w-full p-2 border rounded" placeholder="Total"></td>
            <td class="p-2"><input type="number" name="qual[${rowCount}][marks_obtained]" class="w-full p-2 border rounded" placeholder="Obt"></td>
            <td class="p-2"><input type="number" name="qual[${rowCount}][marks]" class="w-full p-2 border rounded" placeholder="%"></td>
            <td class="p-2"><input type="text" name="qual[${rowCount}][div]" class="w-full p-2 border rounded uppercase" placeholder="Div"></td>
            <td class="p-2 text-center">
                <button type="button" onclick="this.closest('tr').remove()" class="text-red-500 hover:text-red-700">
                    <i data-lucide="trash-2" size="18"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);

        // Initialize TomSelect for the new row if applicable
        const newExamInput = row.querySelector('.exam-tom-select');
        if (newExamInput) {
            initTomSelectExam(newExamInput);
        }

        rowCount++;
        lucide.createIcons();
    }

    document.getElementById('profileUpdateForm').addEventListener('submit', function() {
        const btn = document.getElementById('saveBtn');
        const spinner = document.getElementById('saveSpinner');
        const btnText = document.getElementById('saveBtnText');
        const btnIcon = document.getElementById('saveBtnIcon');
        
        btn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.innerText = 'Saving Changes...';
        btnIcon.classList.add('hidden');
    });

    // Address Checkbox Logic
    const sameAsPresentCheckbox = document.getElementById('sameAsPresent');
    if (sameAsPresentCheckbox) {
        sameAsPresentCheckbox.addEventListener('change', function() {
            if (this.checked) {
                const fields = ['village_town', 'district', 'state', 'country', 'pin_code'];
                fields.forEach(field => {
                    const present = document.querySelector(`[name="present_${field}"]`);
                    const permanent = document.querySelector(`[name="permanent_${field}"]`);
                    if (present && permanent) {
                        permanent.value = present.value;
                        // Optional: trigger input event if needed for validation
                        permanent.dispatchEvent(new Event('input'));
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
