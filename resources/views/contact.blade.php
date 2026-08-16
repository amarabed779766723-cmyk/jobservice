@extends('layouts.app')
@section('title', __('Contact Us') . ' - Job Service')
@section('content')

<div style="max-width:900px; margin:0 auto; padding:1rem;">

    {{-- ========== شريط التنقل بين الأقسام ========== --}}
    <div style="display:flex; gap:0.5rem; flex-wrap:wrap; justify-content:center; margin-bottom:2rem; position:sticky; top:60px; z-index:50; background:var(--bg); padding:0.75rem; border-radius:1rem;">
        <a href="#about" class="btn btn-outline btn-sm" style="border-radius:2rem;">🏢 {{ __('About Us') }}</a>
        <a href="#how" class="btn btn-outline btn-sm" style="border-radius:2rem;">📖 {{ __('How to Use') }}</a>
        <a href="#faq" class="btn btn-outline btn-sm" style="border-radius:2rem;">❓ {{ __('FAQ') }}</a>
        <a href="#community" class="btn btn-outline btn-sm" style="border-radius:2rem;">🌍 {{ __('Community') }}</a>
        <a href="#contact" class="btn btn-primary btn-sm" style="border-radius:2rem;">📧 {{ __('Contact Us') }}</a>
    </div>

    {{-- ========== من نحن ========== --}}
    <section id="about" style="margin-bottom:3rem; scroll-margin-top:120px;">
        <div style="text-align:center; margin-bottom:2rem;">
            <h2 style="font-size:2rem; font-weight:900; color:var(--primary);">🏢 {{ __('About Us') }}</h2>
            <p style="color:var(--text-secondary); max-width:600px; margin:0 auto;">{{ __('About Us Desc') }}</p>
        </div>

        <div class="card" style="padding:2rem; text-align:center;">
            <p style="font-size:1.1rem; line-height:2; color:var(--text-secondary);">
                <strong style="color:var(--primary); font-size:1.3rem;">Job Service</strong> {{ __('About Us Text') }}
            </p>
            <p style="font-size:1.1rem; line-height:2; color:var(--text-secondary); margin-top:1rem;">
                🎯 <strong>{{ __('Our Vision') }}:</strong> {{ __('Our Vision Text') }}
            </p>
            <p style="font-size:1.1rem; line-height:2; color:var(--text-secondary); margin-top:1rem;">
                💪 <strong>{{ __('Our Values') }}:</strong> {{ __('Trust, Quality, Ease, Professionalism') }}
            </p>
        </div>
    </section>

    {{-- ========== كيف تستخدم ========== --}}
    <section id="how" style="margin-bottom:3rem; scroll-margin-top:120px;">
        <div style="text-align:center; margin-bottom:2rem;">
            <h2 style="font-size:2rem; font-weight:900; color:var(--primary);">📖 {{ __('How to Use') }}</h2>
            <p style="color:var(--text-secondary);">{{ __('4 Simple Steps to Start') }}</p>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1.5rem;">
            <div class="card" style="text-align:center; padding:1.5rem; border-top:4px solid #2563eb;">
                <div style="font-size:2.5rem; margin-bottom:0.5rem;">📝</div>
                <h4>1. {{ __('Register Account') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.9rem;">{{ __('Register Account Desc') }}</p>
            </div>
            <div class="card" style="text-align:center; padding:1.5rem; border-top:4px solid #10b981;">
                <div style="font-size:2.5rem; margin-bottom:0.5rem;">🔍</div>
                <h4>2. {{ __('Search for Service') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.9rem;">{{ __('Search for Service Desc') }}</p>
            </div>
            <div class="card" style="text-align:center; padding:1.5rem; border-top:4px solid #f59e0b;">
                <div style="font-size:2.5rem; margin-bottom:0.5rem;">📅</div>
                <h4>3. {{ __('Book Appointment') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.9rem;">{{ __('Book Appointment Desc') }}</p>
            </div>
            <div class="card" style="text-align:center; padding:1.5rem; border-top:4px solid #ef4444;">
                <div style="font-size:2.5rem; margin-bottom:0.5rem;">⭐</div>
                <h4>4. {{ __('Rate Service') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.9rem;">{{ __('Rate Service Desc') }}</p>
            </div>
        </div>
    </section>

    {{-- ========== أسئلة شائعة ========== --}}
    <section id="faq" style="margin-bottom:3rem; scroll-margin-top:120px;">
        <div style="text-align:center; margin-bottom:2rem;">
            <h2 style="font-size:2rem; font-weight:900; color:var(--primary);">❓ {{ __('FAQ') }}</h2>
        </div>

        <div style="display:grid; gap:1rem;">
            <div class="card" style="cursor:pointer;" onclick="this.querySelector('.faq-answer').style.display = this.querySelector('.faq-answer').style.display === 'none' ? 'block' : 'none'">
                <h4 style="display:flex; justify-content:space-between; align-items:center;">
                    {{ __('FAQ Q1') }}
                    <span style="font-size:1.2rem;">➕</span>
                </h4>
                <p class="faq-answer" style="display:none; color:var(--text-secondary); margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid var(--border);">{{ __('FAQ A1') }}</p>
            </div>
        </div>
    </section>

    {{-- ========== المجتمع ========== --}}
    <section id="community" style="margin-bottom:3rem; scroll-margin-top:120px;">
        <div style="text-align:center; margin-bottom:2rem;">
            <h2 style="font-size:2rem; font-weight:900; color:var(--primary);">🌍 {{ __('Community') }}</h2>
            <p style="color:var(--text-secondary);">{{ __('Follow Us') }}</p>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:1rem;">
            <a href="#" class="card" style="text-align:center; padding:1.5rem; text-decoration:none; color:var(--text); border-top:4px solid #1877f2;">
                <div style="font-size:2.5rem;">📘</div>
                <h4>{{ __('Facebook') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.85rem;">{{ __('Latest News') }}</p>
            </a>
            <a href="#" class="card" style="text-align:center; padding:1.5rem; text-decoration:none; color:var(--text); border-top:4px solid #e4405f;">
                <div style="font-size:2.5rem;">📷</div>
                <h4>{{ __('Instagram') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.85rem;">{{ __('Stories & Offers') }}</p>
            </a>
            <a href="#" class="card" style="text-align:center; padding:1.5rem; text-decoration:none; color:var(--text); border-top:4px solid #1da1f2;">
                <div style="font-size:2.5rem;">🐦</div>
                <h4>{{ __('Twitter') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.85rem;">{{ __('Quick Updates') }}</p>
            </a>
            <a href="#" class="card" style="text-align:center; padding:1.5rem; text-decoration:none; color:var(--text); border-top:4px solid #25d366;">
                <div style="font-size:2.5rem;">💬</div>
                <h4>{{ __('WhatsApp') }}</h4>
                <p style="color:var(--text-secondary); font-size:0.85rem;">{{ __('Direct Support') }}</p>
            </a>
        </div>
    </section>

    {{-- ========== اتصل بنا ========== --}}
    <section id="contact" style="margin-bottom:2rem; scroll-margin-top:120px;">
        <div style="text-align:center; margin-bottom:2rem;">
            <h2 style="font-size:2rem; font-weight:900; color:var(--primary);">📧 {{ __('Contact Us') }}</h2>
            <p style="color:var(--text-secondary);">{{ __('Contact Us Desc') }}</p>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:1.5rem; margin-bottom:2rem;">
            <div class="card" style="text-align:center; padding:2rem;">
                <div style="font-size:3rem;">📨</div>
                <h4>{{ __('Email') }}</h4>
                <a href="mailto:amarabed3344@gmail.com" style="color:var(--primary); font-weight:700; text-decoration:none;">amarabed3344@gmail.com</a>
            </div>
            <div class="card" style="text-align:center; padding:2rem;">
                <div style="font-size:3rem;">📞</div>
                <h4>{{ __('Phone') }}</h4>
                <p style="font-weight:700; direction:ltr;">+967 779766723</p>
            </div>
            <div class="card" style="text-align:center; padding:2rem;">
                <div style="font-size:3rem;">⏰</div>
                <h4>{{ __('Working Hours') }}</h4>
                <p style="color:var(--text-secondary);">{{ __('Sun - Thu, 9 AM - 5 PM') }}</p>
            </div>
        </div>

        <div style="text-align:center; background:linear-gradient(135deg, #2563eb, #1d4ed8); border-radius:1.5rem; padding:2.5rem; color:white;">
            <h2 style="font-weight:700; margin-bottom:0.5rem;">💙 {{ __('We Are Here to Serve You') }}</h2>
            <p style="opacity:0.9;">{{ __('We Are Here to Serve You Desc') }}</p>
        </div>
    </section>

</div>
@endsection