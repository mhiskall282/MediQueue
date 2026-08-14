<x-layouts.app title="Software Engineering Documentation & User Guides">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Docs Header Banner --}}
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white mb-8 border border-slate-800 shadow-xl relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-3">
                        <span>📚</span> Engineering Documentation & User Guides
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-white">MediQueue Technical Reference</h1>
                    <p class="text-indigo-200 mt-2 text-sm sm:text-base max-w-2xl">
                        Comprehensive architecture specifications, SRS requirements, UCP effort estimations, deployment blueprints, and operational user guides.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="https://github.com/mhiskall282/ug-swe-exams" target="_blank" class="btn btn-secondary bg-white/10 text-white border-white/20 hover:bg-white/20 backdrop-blur-sm text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-1 fill-current" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                        </svg>
                        GitHub Repository
                    </a>
                    <a href="{{ route('display') }}" target="_blank" class="btn btn-primary bg-indigo-600 hover:bg-indigo-700 text-xs sm:text-sm shadow-md">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse mr-1"></span>
                        Hospital Live Screen
                    </a>
                </div>
            </div>
        </div>

        {{-- Mobile Section Selector --}}
        <div class="lg:hidden mb-6 card p-3">
            <label for="mobile-docs-nav" class="text-xs font-bold text-slate-500 uppercase block mb-1">Jump to Document Section:</label>
            <select id="mobile-docs-nav" onchange="window.location.hash = this.value" class="form-input text-sm">
                <option value="#overview">1. Project Overview & Scope</option>
                <option value="#architecture">2. Architecture & Tech Stack</option>
                <option value="#state-machine">3. Queue State Machine</option>
                <option value="#srs">4. Requirements (SRS & Matrix)</option>
                <option value="#estimation">5. Effort Estimation (UCP)</option>
                <option value="#guides">6. User Guides & Credentials</option>
                <option value="#devops">7. Deployment & Docker</option>
                <option value="#security">8. Security & Governance</option>
            </select>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Desktop Sidebar Navigation --}}
            <aside class="hidden lg:block lg:col-span-3">
                <div class="sticky top-24 card p-5 space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block px-3 py-1">Table of Contents</span>
                    <a href="#overview" class="nav-item text-xs font-semibold py-2">
                        1. Project Overview
                    </a>
                    <a href="#architecture" class="nav-item text-xs font-semibold py-2">
                        2. System Architecture
                    </a>
                    <a href="#state-machine" class="nav-item text-xs font-semibold py-2">
                        3. Queue State Machine
                    </a>
                    <a href="#srs" class="nav-item text-xs font-semibold py-2">
                        4. Requirements Specification
                    </a>
                    <a href="#estimation" class="nav-item text-xs font-semibold py-2">
                        5. Effort Estimation (UCP)
                    </a>
                    <a href="#guides" class="nav-item text-xs font-semibold py-2">
                        6. User Guides & Accounts
                    </a>
                    <a href="#devops" class="nav-item text-xs font-semibold py-2">
                        7. Deployment & Docker
                    </a>
                    <a href="#security" class="nav-item text-xs font-semibold py-2">
                        8. Security & Least Privilege
                    </a>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <div class="lg:col-span-9 space-y-12">

                {{-- SECTION 1: OVERVIEW --}}
                <section id="overview" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 01</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">1. Project Overview & Purpose</h2>
                    </div>

                    <div class="prose prose-slate max-w-none text-sm leading-relaxed space-y-4 text-slate-600">
                        <p>
                            <strong>MediQueue</strong> is a modern, responsive, web-based clinic queue management platform built for outpatient healthcare facilities. It replaces stressful physical waiting lines and verbal callouts with digital queue ticketing, real-time position countdowns, and synchronized public hospital departure screens.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 my-6 not-prose">
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <span class="text-xs font-bold text-slate-500 uppercase block">Examination Scope</span>
                                <p class="text-base font-bold text-slate-900 mt-1">48-Hour Capstone</p>
                                <p class="text-xs text-slate-500 mt-0.5">Advanced Software Engineering</p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <span class="text-xs font-bold text-slate-500 uppercase block">Core Framework</span>
                                <p class="text-base font-bold text-slate-900 mt-1">Laravel 12 + Blade</p>
                                <p class="text-xs text-slate-500 mt-0.5">PHP 8.2 + Tailwind CSS v4</p>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <span class="text-xs font-bold text-slate-500 uppercase block">Test Verification</span>
                                <p class="text-base font-bold text-emerald-600 mt-1">43 Tests / 158 Assertions</p>
                                <p class="text-xs text-slate-500 mt-0.5">100% Automated Test Pass</p>
                            </div>
                        </div>

                        <h3 class="text-base font-bold text-slate-900 pt-2">Scope & Boundary Governance (ADR-001)</h3>
                        <p>
                            To maintain engineering rigor within the university examination constraints, MediQueue is strictly scoped to <strong>administrative clinic flow and queue coordination</strong>. It explicitly excludes electronic medical health records (EHR/EMR), clinical diagnostic algorithms, pharmaceutical billing, and insurance claims.
                        </p>
                    </div>
                </section>

                {{-- SECTION 2: ARCHITECTURE --}}
                <section id="architecture" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 02</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">2. System Architecture & Tech Stack</h2>
                    </div>

                    <div class="text-sm text-slate-600 space-y-4 leading-relaxed">
                        <p>
                            MediQueue is architected as a <strong>Modular Layered Monolith</strong>. All queue business operations, state transitions, atomic numbering, and notification dispatches are centralized in <code>App\Services\QueueService</code>.
                        </p>

                        <div class="bg-slate-900 text-slate-100 rounded-2xl p-5 my-6 font-mono text-xs overflow-x-auto">
                            <pre class="leading-loose">
[ Web Presentation Layer ] ── Blade Components + Tailwind CSS v4
       │
       ▼
[ Routing & Middleware ]   ── RoleMiddleware (patient/staff/admin) + Throttle Guards
       │
       ▼
[ Controller Layer ]       ── Patient, Staff, Admin & Display Controllers
       │
       ▼
[ Business Domain Service] ── QueueService (DB Transactions + Pessimistic Row Locking)
       │
       ▼
[ Data Persistence ]       ── Eloquent Models (User, Service, QueueEntry, Notification, Setting, AuditLog)
       │
       ▼
[ Relational Store ]       ── SQLite / MySQL Relational Database</pre>
                        </div>
                    </div>
                </section>

                {{-- SECTION 3: STATE MACHINE --}}
                <section id="state-machine" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 03</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">3. Queue State Machine</h2>
                    </div>

                    <div class="text-sm text-slate-600 space-y-4 leading-relaxed">
                        <p>
                            Queue tickets strictly transition through an enforced state machine. Invalid state mutations (e.g. attempting to complete an uncalled ticket) throw validation exceptions.
                        </p>

                        <div class="overflow-x-auto my-4">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Current State</th>
                                        <th>Target State</th>
                                        <th>Triggering Actor</th>
                                        <th>Business Meaning</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-waiting">WAITING</span></td>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td>Staff</td>
                                        <td>Staff calls next patient by priority & sequence.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-waiting">WAITING</span></td>
                                        <td><span class="badge badge-cancelled">CANCELLED</span></td>
                                        <td>Patient / Admin</td>
                                        <td>Patient withdraws ticket before consultation.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td><span class="badge badge-in-service">IN_SERVICE</span></td>
                                        <td>Staff</td>
                                        <td>Patient arrives and medical consultation begins.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td><span class="badge badge-skipped">SKIPPED</span></td>
                                        <td>Staff</td>
                                        <td>Patient does not respond to callout.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-skipped">SKIPPED</span></td>
                                        <td><span class="badge badge-called">CALLED</span></td>
                                        <td>Staff</td>
                                        <td>Patient reports to desk and is recalled to queue.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-in-service">IN_SERVICE</span></td>
                                        <td><span class="badge badge-completed">COMPLETED</span></td>
                                        <td>Staff</td>
                                        <td>Consultation concluded (Terminal State).</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                {{-- SECTION 4: SRS --}}
                <section id="srs" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 04</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">4. Requirements Traceability Matrix (RTM)</h2>
                    </div>

                    <div class="text-sm text-slate-600 space-y-4">
                        <p>
                            All requirements from <a href="file:///c:/Users/user/Desktop/ug-swe-exams/docs/SRS.md" class="text-indigo-600 font-semibold hover:underline">docs/SRS.md</a> are mapped directly to implementation components and automated verification tests.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Req ID</th>
                                        <th>Feature Requirement</th>
                                        <th>Implementing Component</th>
                                        <th>Verification Test</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-AUTH-001</td>
                                        <td>Patient Registration & Hashing</td>
                                        <td><code>RegisterController</code></td>
                                        <td><code>AuthTest::test_patient_can_register</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-AUTH-006</td>
                                        <td>Role-Based Access Control (RBAC)</td>
                                        <td><code>RoleMiddleware</code></td>
                                        <td><code>AuthorizationTest</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-QUEUE-001</td>
                                        <td>Atomic Ticket Numbering</td>
                                        <td><code>QueueService::join</code></td>
                                        <td><code>QueueLifecycleTest::test_patient_can_join_active_queue</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-QUEUE-004</td>
                                        <td>Duplicate Ticket Prevention</td>
                                        <td><code>QueueService::join</code></td>
                                        <td><code>QueueLifecycleTest::test_duplicate_prevented</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-QUEUE-008</td>
                                        <td>Staff Call Next Operation</td>
                                        <td><code>Staff\QueueController</code></td>
                                        <td><code>QueueLifecycleTest::test_staff_can_call_next</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-DISP-001</td>
                                        <td>Hospital Screen TV Display</td>
                                        <td><code>DisplayController</code></td>
                                        <td><code>AdminEnhancementsTest::test_hospital_display</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-NOTIF-001</td>
                                        <td>Transactional Email & Alerts</td>
                                        <td><code>QueueNotificationMail</code></td>
                                        <td><code>AdminEnhancementsTest::test_email_dispatch</code></td>
                                    </tr>
                                    <tr>
                                        <td class="font-mono text-xs font-bold">REQ-AUDIT-001</td>
                                        <td>Immutable Security Audit Trail</td>
                                        <td><code>AuditLog Model</code></td>
                                        <td><code>SmokeTest & LifecycleTests</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                {{-- SECTION 5: ESTIMATION --}}
                <section id="estimation" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 05</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">5. Software Effort Estimation (Use Case Points)</h2>
                    </div>

                    <div class="text-sm text-slate-600 space-y-4">
                        <p>
                            Conducted in accordance with Karner's algorithmic <strong>Use Case Points (UCP)</strong> method before implementation began.
                        </p>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200 text-center">
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">Unadjusted UCP</span>
                                <span class="text-xl font-bold text-slate-900">164 UCP</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">Tech Factor (TCF)</span>
                                <span class="text-xl font-bold text-slate-900">0.935</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">Env Factor (ECF)</span>
                                <span class="text-xl font-bold text-slate-900">0.590</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 font-medium block">Adjusted UCP</span>
                                <span class="text-xl font-bold text-indigo-600">~90 UCP</span>
                            </div>
                        </div>

                        <p class="text-xs text-slate-500">
                            Total effort calculated: <strong>46 person-hours</strong> (utilizing a 10% contingency buffer to comfortably fit within the 48-hour individual examination window).
                        </p>
                    </div>
                </section>

                {{-- SECTION 6: USER GUIDES & CREDENTIALS --}}
                <section id="guides" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 06</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">6. User Guides & Seeded Demo Accounts</h2>
                    </div>

                    <div class="space-y-6 text-sm text-slate-600">
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Role Type</th>
                                        <th>Email Address</th>
                                        <th>Password</th>
                                        <th>Accessible Features</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-called">Administrator</span></td>
                                        <td class="font-mono text-xs font-bold text-slate-900">admin@mediqueue.test</td>
                                        <td class="font-mono text-xs">password</td>
                                        <td>Full system control, services CRUD, user password resets, clinic settings, audit trail.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-in-service">Doctor / Staff</span></td>
                                        <td class="font-mono text-xs font-bold text-slate-900">dr.sarah@mediqueue.test</td>
                                        <td class="font-mono text-xs">password</td>
                                        <td>Department queue console, Call Next CTA, start/complete consultation, skip & recall.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-in-service">Nurse / Staff</span></td>
                                        <td class="font-mono text-xs font-bold text-slate-900">nurse.james@mediqueue.test</td>
                                        <td class="font-mono text-xs">password</td>
                                        <td>Nursing queue management and triage flow.</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-waiting">Patient</span></td>
                                        <td class="font-mono text-xs font-bold text-slate-900">john.doe@example.com</td>
                                        <td class="font-mono text-xs">password</td>
                                        <td>Queue ticket selection, live position monitor with 4s polling, cancellation, history.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                {{-- SECTION 7: DEVOPS --}}
                <section id="devops" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 07</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">7. Production Deployment & Docker</h2>
                    </div>

                    <div class="text-sm text-slate-600 space-y-4">
                        <p>
                            MediQueue features a multi-stage Docker container packaging Node 22 for asset compilation, PHP 8.2-FPM for backend processing, and Nginx managed by Supervisor.
                        </p>

                        <div class="bg-slate-900 text-slate-100 rounded-xl p-4 font-mono text-xs overflow-x-auto space-y-2">
                            <p class="text-slate-400"># Run with Docker Compose</p>
                            <p class="text-emerald-400">docker compose up -d --build</p>
                            <p class="text-slate-400"># Access in browser</p>
                            <p class="text-emerald-400">http://localhost:8000</p>
                        </div>
                    </div>
                </section>

                {{-- SECTION 8: SECURITY --}}
                <section id="security" class="card p-6 sm:p-10 scroll-mt-24">
                    <div class="border-b border-slate-100 pb-4 mb-6">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest block mb-1">Section 08</span>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">8. Security First & Principle of Least Privilege</h2>
                    </div>

                    <div class="text-sm text-slate-600 space-y-3">
                        <ul class="list-disc list-inside space-y-2">
                            <li><strong>Bcrypt Password Hashing</strong>: Cost factor 12 in production, cost factor 4 in testing.</li>
                            <li><strong>Strict Role Middleware</strong>: Zero vertical privilege escalation. Non-admins cannot access admin APIs.</li>
                            <li><strong>Rate Limiting</strong>: 10 req/min on authentication; 30 req/min on queue join endpoints.</li>
                            <li><strong>Immutable Event Logs</strong>: Append-only audit table records actor ID, action name, entity ID, metadata JSON, and client IP.</li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
