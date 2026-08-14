<x-layouts.app title="Documentation Hub — Technical & Non-Technical Guides">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Hero Header Banner --}}
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white mb-8 border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-3">
                        <span>📖</span> Complete Technical & Non-Technical Guides
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white">MediQueue Documentation & User Portal</h1>
                    <p class="text-indigo-200 mt-2 text-sm sm:text-base max-w-2xl">
                        Step-by-step walkthroughs for patients, doctors, and administrators, alongside full software engineering architecture and deployment references.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="https://mediqueue-25vl.onrender.com" target="_blank" class="btn btn-secondary bg-emerald-500/20 text-emerald-200 border-emerald-500/30 hover:bg-emerald-500/30 backdrop-blur-sm text-xs sm:text-sm font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        🌐 Live App
                    </a>
                    <a href="https://github.com/mhiskall282/MediQueue" target="_blank" class="btn btn-secondary bg-white/10 text-white border-white/20 hover:bg-white/20 backdrop-blur-sm text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                        GitHub
                    </a>
                    <a href="{{ route('display') }}" target="_blank" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-xs sm:text-sm shadow-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1"></span>
                        Hospital Live Screen
                    </a>
                </div>
            </div>
        </div>

        {{-- Guide Audience Switcher Tabs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <a href="#non-technical-track" class="card p-5 border-2 border-emerald-500/30 bg-emerald-50/20 hover:border-emerald-500 transition-all flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-2xl flex-shrink-0">
                    🟢
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block">User Friendly Guide</span>
                    <h3 class="font-extrabold text-slate-900 text-base">For Patients, Doctors & Admins</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Step-by-step instructions on using every feature.</p>
                </div>
            </a>

            <a href="#technical-track" class="card p-5 border-2 border-indigo-500/30 bg-indigo-50/20 hover:border-indigo-500 transition-all flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-2xl flex-shrink-0">
                    🔵
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700 block">Engineering Reference</span>
                    <h3 class="font-extrabold text-slate-900 text-base">For Developers & Evaluators</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Architecture, SRS, UCP estimation, Docker, security & tests.</p>
                </div>
            </a>
        </div>

        {{-- Mobile Section Selector --}}
        <div class="lg:hidden mb-6 card p-3">
            <label for="mobile-docs-nav" class="text-xs font-bold text-slate-500 uppercase block mb-1">Jump to Guide Section:</label>
            <select id="mobile-docs-nav" onchange="window.location.hash = this.value" class="form-input text-sm">
                <optgroup label="🟢 Non-Technical User Guides">
                    <option value="#patient-guide">Patient: How to Join & Track Queue</option>
                    <option value="#staff-guide">Doctor / Staff: Managing Queue & Consultations</option>
                    <option value="#admin-guide">Admin: Users, Passwords & Clinic Settings</option>
                    <option value="#tv-guide">Waiting Room: Launching Hospital TV Screen</option>
                </optgroup>
                <optgroup label="🔵 Technical Engineering References">
                    <option value="#architecture">System Architecture & Layered Monolith</option>
                    <option value="#state-machine">Queue State Machine & Transaction Rules</option>
                    <option value="#srs">SRS & Requirements Traceability Matrix</option>
                    <option value="#estimation">Software Effort Estimation (UCP)</option>
                    <option value="#devops">Docker & Cloud Deployment</option>
                    <option value="#security">Security & Least Privilege RBAC</option>
                </optgroup>
            </select>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Desktop Sidebar Navigation --}}
            <aside class="hidden lg:block lg:col-span-3">
                <div class="sticky top-24 card p-5 space-y-3">
                    <div>
                        <span class="text-[11px] font-black uppercase tracking-wider text-emerald-700 block px-3 py-1 bg-emerald-50 rounded-md mb-1">
                            🟢 Non-Technical Guides
                        </span>
                        <div class="space-y-0.5 mt-1">
                            <a href="#patient-guide" class="nav-item text-xs font-medium py-1.5">
                                👤 Patient User Guide
                            </a>
                            <a href="#staff-guide" class="nav-item text-xs font-medium py-1.5">
                                👨‍⚕️ Clinical Staff Guide
                            </a>
                            <a href="#admin-guide" class="nav-item text-xs font-medium py-1.5">
                                🔧 Administrator Guide
                            </a>
                            <a href="#tv-guide" class="nav-item text-xs font-medium py-1.5">
                                📺 Hospital TV Screen Guide
                            </a>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[11px] font-black uppercase tracking-wider text-indigo-700 block px-3 py-1 bg-indigo-50 rounded-md mb-1">
                            🔵 Technical Reference
                        </span>
                        <div class="space-y-0.5 mt-1">
                            <a href="#architecture" class="nav-item text-xs font-medium py-1.5">
                                🏗️ System Architecture
                            </a>
                            <a href="#state-machine" class="nav-item text-xs font-medium py-1.5">
                                🔄 Queue State Machine
                            </a>
                            <a href="#srs" class="nav-item text-xs font-medium py-1.5">
                                📋 Requirements Matrix (RTM)
                            </a>
                            <a href="#estimation" class="nav-item text-xs font-medium py-1.5">
                                ⏱️ Effort Estimation (UCP)
                            </a>
                            <a href="#devops" class="nav-item text-xs font-medium py-1.5">
                                🚢 Docker & Deployment
                            </a>
                            <a href="#security" class="nav-item text-xs font-medium py-1.5">
                                🛡️ Security & Least Privilege
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Main Content Column --}}
            <div class="lg:col-span-9 space-y-12">

                {{-- ========================================================================= --}}
                {{-- TRACK 1: NON-TECHNICAL STEP-BY-STEP USER GUIDES --}}
                {{-- ========================================================================= --}}

                <div id="non-technical-track" class="space-y-8 scroll-mt-24">
                    <div class="border-b-2 border-emerald-500 pb-3">
                        <span class="text-xs font-black uppercase tracking-widest text-emerald-600 block">Part One</span>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">🟢 Step-by-Step User Guides</h2>
                        <p class="text-slate-500 text-sm mt-1">Non-technical walkthroughs for everyday clinic operation.</p>
                    </div>

                    {{-- 1. PATIENT GUIDE --}}
                    <section id="patient-guide" class="card p-6 sm:p-8 scroll-mt-24 border-l-4 border-emerald-500">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-10 h-10 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center text-xl font-bold">👤</span>
                            <div>
                                <h3 class="text-xl font-extrabold text-slate-900">Patient Guide: How to Join & Track Your Queue</h3>
                                <p class="text-xs text-slate-500">No more waiting in physical lines at the clinic reception.</p>
                            </div>
                        </div>

                        <div class="space-y-6 text-sm text-slate-600">
                            {{-- Step 1 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">1</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Create an Account or Sign In</h4>
                                    <p class="mt-1">
                                        Click <strong>"Get Started"</strong> on the homepage. Enter your Name, Email, and Password. If you already have an account, click <strong>"Sign In"</strong>.
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">Demo Patient Account: <code>john.doe@example.com</code> / <code>password</code></p>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">2</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Choose a Clinic Service</h4>
                                    <p class="mt-1">
                                        Go to <strong>"Join Queue"</strong> in the top menu. You will see all available departments (e.g. <em>General Consultation</em>, <em>Nursing</em>, <em>Pharmacy</em>) with their average wait times and current queue lengths.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">3</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Take Your Digital Queue Ticket</h4>
                                    <p class="mt-1">
                                        Click <strong>"Select & Join Queue"</strong>, review the estimated wait, and click <strong>"Confirm & Issue Queue Ticket"</strong>. You will immediately be assigned a unique ticket number (e.g. <strong>GC-005</strong>).
                                    </p>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">4</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Track Your Real-Time Position from Anywhere</h4>
                                    <p class="mt-1">
                                        Your screen automatically refreshes every 4 seconds. You will see:
                                    </p>
                                    <ul class="list-disc list-inside mt-2 space-y-1 text-xs text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-200">
                                        <li><strong>Your exact Position:</strong> (e.g. <em>#1 in line</em>)</li>
                                        <li><strong>People Ahead of You:</strong> (e.g. <em>0 people ahead</em>)</li>
                                        <li><strong>Estimated Wait:</strong> (e.g. <em>~0 minutes</em>)</li>
                                        <li><strong>Currently Serving:</strong> Shows who is in consultation right now.</li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Step 5 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">5</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">When Called: "It's Your Turn!"</h4>
                                    <p class="mt-1">
                                        When the doctor calls your number, your screen will turn bright indigo with an animated <strong>"It is Your Turn!"</strong> banner. You will also receive an automated email alert and in-app notification. Proceed immediately to the consultation room!
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 2. CLINICAL STAFF GUIDE --}}
                    <section id="staff-guide" class="card p-6 sm:p-8 scroll-mt-24 border-l-4 border-indigo-500">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-10 h-10 bg-indigo-100 text-indigo-700 rounded-xl flex items-center justify-center text-xl font-bold">👨‍⚕️</span>
                            <div>
                                <h3 class="text-xl font-extrabold text-slate-900">Clinical Staff Guide: Doctor & Nurse Console</h3>
                                <p class="text-xs text-slate-500">How to call patients, manage consultations, and handle no-shows.</p>
                            </div>
                        </div>

                        <div class="space-y-6 text-sm text-slate-600">
                            {{-- Step 1 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">1</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Sign in with Staff Credentials</h4>
                                    <p class="mt-1">
                                        Sign in with your staff account (e.g. <code>dr.sarah@mediqueue.test</code> / <code>password</code>). You will be redirected straight to the <strong>Staff Queue Console</strong>.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">2</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Select Your Department</h4>
                                    <p class="mt-1">
                                        Use the top-right department dropdown to choose which queue you are serving (e.g. <em>General Consultation</em>, <em>Lab</em>, or <em>Nursing</em>).
                                    </p>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">3</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Calling the Next Patient</h4>
                                    <p class="mt-1">
                                        Click the big green button: <strong>"Call Next Patient"</strong>. The system selects the next eligible patient according to priority and sequence. This instantly updates the patient's phone and broadcasts their number to the waiting room TV screen!
                                    </p>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">4</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">5-Tier Manchester Emergency Triage Assessment</h4>
                                    <p class="mt-1">
                                        Staff can assess and update any waiting patient's triage severity with one click:
                                    </p>
                                    <ul class="list-disc pl-5 mt-2 space-y-1 text-xs">
                                        <li><strong class="text-red-600">🔴 Red (P1):</strong> Resuscitation / Immediate Emergency (Auto-escalates to Urgent)</li>
                                        <li><strong class="text-orange-600">🟠 Orange (P2):</strong> Very Urgent (10-minute target)</li>
                                        <li><strong class="text-yellow-600">🟡 Yellow (P3):</strong> Urgent (60-minute target)</li>
                                        <li><strong class="text-emerald-600">🟢 Green (P4):</strong> Standard Outpatient (120-minute target)</li>
                                        <li><strong class="text-blue-600">🔵 Blue (P5):</strong> Non-Urgent (240-minute target)</li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Step 5 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">5</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Diagnostic Laboratory Referral & Auto-Loopback</h4>
                                    <p class="mt-1">
                                        During consultation, expand <strong>"🧪 Order Lab Investigation & Transfer"</strong>. The patient transitions seamlessly to the Laboratory queue. When the lab technician inputs test findings, the ticket is <em>automatically returned</em> to the referring doctor with retained <strong>Orange / Urgent</strong> priority for immediate review!
                                    </p>
                                </div>
                            </div>

                            {{-- Step 6 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">6</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">🚨 Emergency Unconscious Trauma Protocol (Code Red)</h4>
                                    <p class="mt-1">
                                        When an unconscious, unidentified patient arrives without ID, click <strong>"Emergency Trauma"</strong> from the Left Sidebar. The system generates a temporary Trauma MRN (e.g. <code>EMG-DOE-4821</code>), allocates an Emergency Triage Bay, sets 🔴 <strong>Red Triage</strong>, and broadcasts a Code Red emergency page to all active on-call doctors. When identified, staff can link the trauma ticket to their permanent verified MRN.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 7 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">7</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Discharge, Care Summary & PDF Reporting</h4>
                                    <p class="mt-1">
                                        Concluding a consultation automatically releases the allocated bed, records final clinical notes, and dispatches a complete care summary email to the patient. Administrators can download streaming CSVs and formatted PDF operational summary reports from the <strong>Reports & Analytics</strong> portal.
                                    </p>
                                </div>
                            </div>

                            {{-- Previous Step 4 (Now 8) --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">8</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Starting and Concluding Service</h4>
                                    <ul class="list-disc list-inside mt-2 space-y-1.5 text-xs text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-200">
                                        <li><strong>Start Service:</strong> When the patient enters the room, click "Start Service" to begin timing the consultation.</li>
                                        <li><strong>Complete Service:</strong> When done, click "Complete Service" to archive the ticket and compute accurate average wait times.</li>
                                        <li><strong>Skip Patient:</strong> If the patient did not enter the room after multiple calls, click "Skip".</li>
                                        <li><strong>Recall Patient:</strong> If a skipped patient returns to the desk, you can click "Recall" to put them back into consultation status.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 3. ADMINISTRATOR GUIDE --}}
                    <section id="admin-guide" class="card p-6 sm:p-8 scroll-mt-24 border-l-4 border-amber-500">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center text-xl font-bold">🔧</span>
                            <div>
                                <h3 class="text-xl font-extrabold text-slate-900">Administrator Guide: Governance, Users & Settings</h3>
                                <p class="text-xs text-slate-500">Managing clinic departments, resetting passwords, and system audits.</p>
                            </div>
                        </div>

                        <div class="space-y-6 text-sm text-slate-600">
                            {{-- Step 1 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">1</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Access Admin Dashboard</h4>
                                    <p class="mt-1">
                                        Log in with <code>admin@mediqueue.test</code> / <code>password</code>. View clinic KPIs: total patients, active services, waiting count, and average wait time.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">2</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Managing Clinic Services</h4>
                                    <p class="mt-1">
                                        Navigate to <strong>"Services"</strong>. You can create new departments (e.g. <em>Dental</em>, <em>Pediatrics</em>), edit prefixes (e.g. <code>DEN</code>), adjust consultation durations, or activate/deactivate departments without downtime.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">3</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Medical Staff Licensing Vetting & Privilege Extension</h4>
                                    <p class="mt-1">
                                        Under <strong>"System Users & Roles"</strong>, administrators inspect applicant medical licenses (e.g. <code>MMC-748921</code>), assign clinical roles (Doctor, Nurse, Pharmacist, Lab Tech, or Front Desk Staff), approve/revoke access, and dynamically extend custom capabilities via the <strong>Dynamic Privilege Extension</strong> matrix.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">4</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">🛡️ HIPAA & ISO 27001 Security Incident Telemetry</h4>
                                    <p class="mt-1">
                                        Inspect continuous anomaly detection telemetry under <strong>"Security & Compliance"</strong>. The engine tracks brute-force login attempts, unauthorized route access, privilege escalations, and client IP addresses, enabling one-click audit mitigation.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 5 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">5</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">User Creation & Password Resets</h4>
                                    <p class="mt-1">
                                        Navigate to <strong>"Users"</strong>. Click <strong>"Create Account"</strong> to add doctors, nurses, or admins. Click <strong>"Edit"</strong> on any user row to update profile details or perform an <strong>Administrative Password Reset</strong> (which automatically emails the new credentials to the user).
                                    </p>
                                </div>
                            </div>

                            {{-- Step 4 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">4</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Configuring Clinic Information & Settings</h4>
                                    <p class="mt-1">
                                        Navigate to <strong>"Settings"</strong>. Change the clinic name, contact phone, physical address, operating hours, and toggle automated email alerts.
                                    </p>
                                </div>
                            </div>

                            {{-- Step 5 --}}
                            <div class="flex items-start gap-4">
                                <span class="w-7 h-7 rounded-full bg-amber-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">5</span>
                                <div>
                                    <h4 class="font-bold text-slate-900">Security Audit Trail</h4>
                                    <p class="mt-1">
                                        Navigate to <strong>"Audit"</strong>. Review the immutable, timestamped record of every action taken in the system, complete with actor names, target records, JSON metadata, and client IP addresses.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- 4. HOSPITAL TV DISPLAY GUIDE --}}
                    <section id="tv-guide" class="card p-6 sm:p-8 scroll-mt-24 border-l-4 border-slate-700 bg-slate-900 text-white">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center text-xl font-bold">📺</span>
                            <div>
                                <h3 class="text-xl font-extrabold text-white">Hospital Waiting Room TV Display Guide</h3>
                                <p class="text-xs text-slate-400">How to set up the public departure board screen on waiting area TVs or tablets.</p>
                            </div>
                        </div>

                        <div class="space-y-4 text-sm text-slate-300">
                            <p>
                                Open <strong><code>/display</code></strong> on any Smart TV browser or tablet in the clinic waiting room:
                            </p>
                            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 font-mono text-xs text-emerald-400">
                                https://your-domain.com/display
                            </div>
                            <ul class="list-disc list-inside space-y-2 text-xs text-slate-400">
                                <li><strong>Fullscreen Mode:</strong> Click the expand icon in the top right to make the display fill the entire TV screen with zero browser chrome.</li>
                                <li><strong>Automatic Refresh:</strong> The screen polls the server every 3 seconds — no manual refresh required.</li>
                                <li><strong>Audio & Visual Chime:</strong> When a new patient is called, the number flashes in bold white and indigo on the left panel.</li>
                            </ul>
                        </div>
                    </section>
                </div>

                {{-- ========================================================================= --}}
                {{-- TRACK 2: TECHNICAL & SOFTWARE ENGINEERING SPECIFICATIONS --}}
                {{-- ========================================================================= --}}

                <div id="technical-track" class="space-y-8 scroll-mt-24 pt-8 border-t-2 border-slate-200">
                    <div class="border-b-2 border-indigo-500 pb-3">
                        <span class="text-xs font-black uppercase tracking-widest text-indigo-600 block">Part Two</span>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tight">🔵 Software Engineering & Technical Reference</h2>
                        <p class="text-slate-500 text-sm mt-1">Architectural specifications, algorithms, estimation models, and deployment configurations.</p>
                    </div>

                    {{-- 1. SYSTEM ARCHITECTURE --}}
                    <section id="architecture" class="card p-6 sm:p-8 scroll-mt-24">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-4">1. System Architecture (ADR-002)</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            MediQueue follows a <strong>Modular Layered Monolith</strong> architecture using Laravel 12 on PHP 8.2 with Blade templates and Tailwind CSS v4. Database queries on active queues employ pessimistic locking (<code>lockForUpdate</code>) inside atomic transactions to eliminate race conditions.
                        </p>

                        <div class="bg-slate-900 text-slate-100 rounded-2xl p-5 font-mono text-xs overflow-x-auto">
                            <pre class="leading-loose">
[ Client Tier ]      ── Web Browsers / Mobile Clients / Hospital TV Kiosks
       │
       ▼
[ Web Presentation ] ── Laravel Blade Layouts (x-layouts.app) + Tailwind v4 Design Tokens
       │
       ▼
[ Security Guard ]   ── RoleMiddleware (patient | staff | admin) + RateLimiter Throttling
       │
       ▼
[ Controllers ]      ── Patient, Staff, Admin & Display Controllers
       │
       ▼
[ Domain Service ]   ── QueueService (Atomic transactions, sequence generation, state transitions)
       │
       ▼
[ Persistence Tier]  ── Eloquent Models (User, Service, QueueEntry, Notification, Setting, AuditLog)
       │
       ▼
[ Storage Tier ]     ── Relational DB (SQLite for dev/testing; MySQL/Postgres for production)</pre>
                        </div>
                    </section>

                    {{-- 2. STATE MACHINE --}}
                    <section id="state-machine" class="card p-6 sm:p-8 scroll-mt-24">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-4">2. Queue State Machine Specification</h3>
                        <p class="text-sm text-slate-600 mb-4">
                            Every ticket follows a formal finite state machine enforced by <code>QueueEntry::canTransitionTo()</code>:
                        </p>
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>From Status</th>
                                        <th>To Status</th>
                                        <th>Actor</th>
                                        <th>Trigger / Guard Condition</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-waiting">WAITING</span></td>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td>Staff</td>
                                        <td>"Call Next" CTA; selects highest priority FIFO ticket.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-waiting">WAITING</span></td>
                                        <td><span class="badge badge-cancelled">CANCELLED</span></td>
                                        <td>Patient / Admin</td>
                                        <td>Patient cancels queue place before being called.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td><span class="badge badge-in-service">IN_SERVICE</span></td>
                                        <td>Staff</td>
                                        <td>Patient enters room; consultation starts.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td><span class="badge badge-skipped">SKIPPED</span></td>
                                        <td>Staff</td>
                                        <td>No-show response; moves to skipped pool.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-skipped">SKIPPED</span></td>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td>Staff</td>
                                        <td>Patient returns to desk; recalled to active call.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-in-service">IN_SERVICE</span></td>
                                        <td><span class="badge badge-completed">COMPLETED</span></td>
                                        <td>Staff</td>
                                        <td>Consultation concluded (Terminal state).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {{-- 3. SRS TRACEABILITY MATRIX --}}
                    <section id="srs" class="card p-6 sm:p-8 scroll-mt-24">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-4">3. Requirements Traceability Matrix (RTM)</h3>
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Req Code</th>
                                        <th>Specification</th>
                                        <th>Priority</th>
                                        <th>Verification Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono font-bold text-xs">REQ-AUTH-001</td>
                                        <td>User Registration & Bcrypt Hashing</td>
                                        <td><span class="badge badge-called">MUST</span></td>
                                        <td><code>AuthTest::test_patient_can_register</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono font-bold text-xs">REQ-AUTH-006</td>
                                        <td>Role-Based Access Control (RBAC)</td>
                                        <td><span class="badge badge-called">MUST</span></td>
                                        <td><code>AuthorizationTest</code> (8 test cases)</td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono font-bold text-xs">REQ-QUEUE-001</td>
                                        <td>Sequential Atomic Ticket Issuing</td>
                                        <td><span class="badge badge-called">MUST</span></td>
                                        <td><code>QueueLifecycleTest::test_sequential_ticket_numbers</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono font-bold text-xs">REQ-QUEUE-004</td>
                                        <td>Duplicate Active Ticket Prevention</td>
                                        <td><span class="badge badge-called">MUST</span></td>
                                        <td><code>QueueLifecycleTest::test_duplicate_prevented</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono font-bold text-xs">REQ-DISP-001</td>
                                        <td>Hospital Screen TV Display</td>
                                        <td><span class="badge badge-in-service">SHOULD</span></td>
                                        <td><code>AdminEnhancementsTest::test_hospital_display</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono font-bold text-xs">REQ-NOTIF-001</td>
                                        <td>Transactional Email Dispatch</td>
                                        <td><span class="badge badge-in-service">SHOULD</span></td>
                                        <td><code>AdminEnhancementsTest::test_email_dispatch</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {{-- 4. ESTIMATION (UCP) --}}
                    <section id="estimation" class="card p-6 sm:p-8 scroll-mt-24">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-4">4. Software Effort Estimation (Use Case Points)</h3>
                        <p class="text-sm text-slate-600 mb-4">
                            Calculated using Gustav Karner's algorithmic Use Case Points formula:
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200 text-center mb-4">
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">Unadjusted UCP</span>
                                <span class="text-xl font-black text-slate-900">164</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">TCF Factor</span>
                                <span class="text-xl font-black text-slate-900">0.935</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">ECF Factor</span>
                                <span class="text-xl font-black text-slate-900">0.590</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">Adjusted UCP</span>
                                <span class="text-xl font-black text-indigo-600">~90 UCP</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500">
                            Total effort: <strong>46 person-hours</strong> (fits within the individual 48-hour examination scope).
                        </p>
                    </section>

                    {{-- 5. DEVOPS & DOCKER --}}
                    <section id="devops" class="card p-6 sm:p-8 scroll-mt-24">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-4">5. Production Deployment & DevOps</h3>
                        <div class="space-y-4 text-sm text-slate-600">
                            <p>
                                MediQueue includes a multi-stage production Dockerfile and automated Render.com Blueprint (<code>render.yaml</code>) that provisions both the <strong>Dockerized Laravel application</strong> and a <strong>Managed PostgreSQL database instance</strong>:
                            </p>
                            <div class="bg-slate-900 text-slate-100 rounded-xl p-4 font-mono text-xs overflow-x-auto space-y-2">
                                <p class="text-slate-400"># 1. Local execution with Docker Compose</p>
                                <p class="text-emerald-400">docker compose up -d --build</p>
                                <p class="text-slate-400"># 2. Deploy to Render via Blueprint</p>
                                <p class="text-indigo-300">Render Dashboard → New Blueprint → Select repo (Auto-provisions PostgreSQL + App)</p>
                                <p class="text-slate-400"># 3. Execute automated test suite</p>
                                <p class="text-emerald-400">php vendor/bin/phpunit</p>
                            </div>
                        </div>
                    </section>

                    {{-- 6. SECURITY --}}
                    <section id="security" class="card p-6 sm:p-8 scroll-mt-24 border-l-4 border-indigo-600">
                        <h3 class="text-xl font-extrabold text-slate-900 mb-4">6. Security Architecture, HIPAA & ISO 27001 Compliance</h3>
                        <div class="text-sm text-slate-600 space-y-3">
                            <ul class="list-disc list-inside space-y-2">
                                <li><strong>Granular Least-Privilege Clinical Roles</strong>: Strict isolation between Doctors, Nurses, Pharmacists, Lab Techs, Receptionists, and Admins.</li>
                                <li><strong>Medical Staff Licensing Gate</strong>: Mandatory practicing license submission (`/staff/onboarding`) and Administrator verification before clinical data access.</li>
                                <li><strong>Mandatory First-Time Password Change</strong>: Forced password reset on administrator creation or reset (`/force-password-change`).</li>
                                <li><strong>Real-Time Anomaly & Sign-In Telemetry</strong>: Automated detection of brute-force login attempts and unrecognized IP address changes with instant transactional email alerts.</li>
                                <li><strong>Live UGMC Zoho SMTP Gateway</strong>: Secure encrypted transactional notifications delivered via SSL port 465 with hospital branding.</li>
                                <li><strong>Immutable Forensic Audit Trail</strong>: Append-only ledger recording every state mutation with SHA-256 integrity metadata.</li>
                            </ul>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
