
    <footer id="contact" class="bg-white pt-16 pb-8 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('image/logo/logo.png') }}" alt="Director of Education, BTC Logo" class="w-10 h-10 object-cover">
                        <span class="font-extrabold text-lg text-slate-900">Online <span class="text-blue-600">Setu</span></span>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">
                        One-stop digital platform for student admissions, school management, and job applications across India.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-colors"><i data-lucide="facebook" size="18"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-colors"><i data-lucide="twitter" size="18"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-colors"><i data-lucide="instagram" size="18"></i></a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:text-blue-600 transition-colors"><i data-lucide="linkedin" size="18"></i></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Our Solutions</h4>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="{{ url('/') }}#management" class="hover:text-blue-600 transition-colors">School & Coaching CRM</a></li>
                        <li><a href="{{ url('/') }}#admissions" class="hover:text-blue-600 transition-colors">Digital Admission Portal</a></li>
                        <li><a href="{{ url('/') }}#finances" class="hover:text-blue-600 transition-colors">Automated Fee Payments</a></li>
                        <li><a href="{{ url('/') }}#academic" class="hover:text-blue-600 transition-colors">Online MCQ Examinations</a></li>
                        <li><a href="{{ url('/') }}#academic" class="hover:text-blue-600 transition-colors">Performance Tracking</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Company</h4>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="{{ route('about') }}" class="hover:text-blue-600 transition-colors">About Us</a></li>
                        <li><a href="{{ route('pricing') }}" class="hover:text-blue-600 transition-colors">Pricing</a></li>
                        <li><a href="{{ route('help') }}" class="hover:text-blue-600 transition-colors">Contact Support</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-blue-600 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-blue-600 transition-colors">Terms of Service</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-6 uppercase text-xs tracking-widest">Support</h4>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li class="flex items-start">
                            <i data-lucide="phone" size="18" class="mr-3 text-blue-600"></i>
                            <span>+91 7099144957</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="mail" size="18" class="mr-3 text-blue-600"></i>
                            <span>info.jypko@gmail.com</span>
                        </li>
                        <li class="flex items-start">
                            <i data-lucide="help-circle" size="18" class="mr-3 text-blue-600"></i>
                            <a href="{{ route('help') }}" class="hover:text-blue-600 transition-colors">Help Center</a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-400">
                <p>&copy; 2026 Director of Education, BTC. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <span>Designed with ❤️ in India</span>
                </div>
            </div>
        </div>
    </footer>
