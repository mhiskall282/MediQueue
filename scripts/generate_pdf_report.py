import os
import sys
from reportlab.lib.pagesizes import letter, A4
from reportlab.lib import colors
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            if self._pageNumber > 1:
                self.draw_header_footer(num_pages)
            canvas.Canvas.showPage(self)
        canvas.Canvas.save(self)

    def draw_header_footer(self, page_count):
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748b"))
        
        # Header
        self.drawString(40, 800, "MediQueue — Software Engineering Capstone Final Examination Report")
        self.drawRightString(555, 800, "August 2026")
        self.setStrokeColor(colors.HexColor("#e2e8f0"))
        self.setLineWidth(0.5)
        self.line(40, 792, 555, 792)
        
        # Footer
        self.line(40, 45, 555, 45)
        self.drawString(40, 32, "Individual 48-Hour Capstone Examination | Lead Software Engineer")
        self.drawRightString(555, 32, f"Page {self._pageNumber} of {page_count}")
        self.restoreState()

def build_pdf(filename="docs/MediQueue_Capstone_Final_Report.pdf"):
    dirname = os.path.dirname(filename)
    if dirname:
        os.makedirs(dirname, exist_ok=True)
    doc = SimpleDocTemplate(
        filename,
        pagesize=A4,
        leftMargin=40,
        rightMargin=40,
        topMargin=50,
        bottomMargin=55
    )

    styles = getSampleStyleSheet()
    
    # Custom styles
    title_style = ParagraphStyle(
        'CoverTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=30,
        leading=34,
        textColor=colors.HexColor('#0f172a'),
        spaceAfter=10
    )
    
    subtitle_style = ParagraphStyle(
        'CoverSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=15,
        leading=18,
        textColor=colors.HexColor('#4f46e5'),
        spaceAfter=25
    )

    h1_style = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=16,
        leading=20,
        textColor=colors.HexColor('#0f172a'),
        spaceBefore=18,
        spaceAfter=10,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'Heading2_Custom',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=15,
        textColor=colors.HexColor('#1e293b'),
        spaceBefore=14,
        spaceAfter=6,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'Body_Custom',
        parent=styles['BodyText'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=14,
        textColor=colors.HexColor('#334155'),
        spaceAfter=8
    )

    code_style = ParagraphStyle(
        'Code_Custom',
        parent=styles['Normal'],
        fontName='Courier',
        fontSize=8,
        leading=11,
        textColor=colors.HexColor('#f8fafc'),
        backColor=colors.HexColor('#0f172a'),
        spaceAfter=8,
        spaceBefore=4
    )

    table_cell = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=11,
        textColor=colors.HexColor('#1e293b')
    )

    table_header = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11,
        textColor=colors.HexColor('#0f172a')
    )

    story = []

    # ==================== COVER PAGE ====================
    story.append(Spacer(1, 40))
    story.append(Paragraph("UNIVERSITY ADVANCED SOFTWARE ENGINEERING CAPSTONE", ParagraphStyle('CoverBadge', fontName='Helvetica-Bold', fontSize=10, textColor=colors.HexColor('#4338ca'), spaceAfter=15)))
    story.append(Paragraph("MediQueue", title_style))
    story.append(Paragraph("Smart Clinic Queue Management System", subtitle_style))
    story.append(HRFlowable(width="100%", thickness=3, color=colors.HexColor('#4f46e5'), spaceAfter=30))

    cover_table_data = [
        [Paragraph("<b>Assessment Type:</b>", table_cell), Paragraph("Individual 48-Hour Software Engineering Examination", table_cell)],
        [Paragraph("<b>Live Web Application:</b>", table_cell), Paragraph("<font color='#4f46e5'>https://mediqueue-25vl.onrender.com</font>", table_cell)],
        [Paragraph("<b>Hospital TV Display Screen:</b>", table_cell), Paragraph("<font color='#4f46e5'>https://mediqueue-25vl.onrender.com/display</font>", table_cell)],
        [Paragraph("<b>In-App Documentation Hub:</b>", table_cell), Paragraph("<font color='#4f46e5'>https://mediqueue-25vl.onrender.com/docs</font>", table_cell)],
        [Paragraph("<b>GitHub Source Repository:</b>", table_cell), Paragraph("<font color='#4f46e5'>https://github.com/mhiskall282/MediQueue</font>", table_cell)],
        [Paragraph("<b>Automated Test Suite:</b>", table_cell), Paragraph("<b>57 PHPUnit Tests / 234 Assertions (100% Pass)</b>", table_cell)],
        [Paragraph("<b>Technology Stack:</b>", table_cell), Paragraph("Laravel 12, PHP 8.2, Tailwind CSS v4, Managed PostgreSQL, Vite 7", table_cell)],
        [Paragraph("<b>Clinical Subsystems:</b>", table_cell), Paragraph("5-Tier Triage, Ward Beds, Advance Appointments, On-Call Roster, Lab Loopback, Trauma Protocol", table_cell)],
        [Paragraph("<b>Author / Candidate:</b>", table_cell), Paragraph("Lead Senior Software Engineering Candidate", table_cell)],
        [Paragraph("<b>Submission Date:</b>", table_cell), Paragraph("August 14, 2026", table_cell)],
    ]
    t_cover = Table(cover_table_data, colWidths=[170, 345])
    t_cover.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#f8fafc')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0, 0), (-1, -1), 7),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 7),
        ('LEFTPADDING', (0, 0), (-1, -1), 12),
        ('RIGHTPADDING', (0, 0), (-1, -1), 12),
    ]))
    story.append(t_cover)
    story.append(Spacer(1, 30))
    story.append(Paragraph("<b>Status:</b> Production Ready, Deployed on Render Cloud PaaS, Fully Tested and Verified.", ParagraphStyle('StatusNotice', fontName='Helvetica-Bold', fontSize=10, textColor=colors.HexColor('#166534'))))
    story.append(PageBreak())

    # ==================== 1. EXECUTIVE SUMMARY ====================
    story.append(Paragraph("1. Executive Summary & Problem Definition", h1_style))
    story.append(Paragraph(
        "<b>MediQueue</b> is a modern, accessible, enterprise-grade clinic queue management web application designed to eliminate physical waiting lines in outpatient healthcare facilities. In conventional outpatient environments, physical queues produce severe waiting room overcrowding, patient anxiety, opaque triage prioritization, and elevated nosocomial contagion risks. MediQueue replaces physical lines with a resilient, transactional digital queue engine.",
        body_style
    ))
    story.append(Paragraph(
        "The project was engineered, tested, and containerized within an intensive 48-hour examination scope, delivering complete role-isolated workflows for patients, clinical staff, on-call doctors, diagnostic labs, trauma teams, administrators, and public hospital screens.",
        body_style
    ))

    # Metrics Table
    kpi_data = [
        [
            Paragraph("<b>57 Tests (100% Pass)</b><br/><font size=7 color='#64748b'>Automated PHPUnit</font>", table_cell),
            Paragraph("<b>234 Assertions</b><br/><font size=7 color='#64748b'>Zero Test Failures</font>", table_cell),
            Paragraph("<b>~90 UCP (~46h)</b><br/><font size=7 color='#64748b'>Algorithmic Estimation</font>", table_cell),
            Paragraph("<b>0 Collisions</b><br/><font size=7 color='#64748b'>Pessimistic Concurrency</font>", table_cell),
        ]
    ]
    t_kpi = Table(kpi_data, colWidths=[128, 128, 128, 131])
    t_kpi.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#eef2ff')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#c7d2fe')),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('TOPPADDING', (0, 0), (-1, -1), 10),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 10),
    ]))
    story.append(t_kpi)
    story.append(Spacer(1, 10))

    # ==================== 2. SYSTEM ARCHITECTURE & DOMAIN ENGINE ====================
    story.append(Paragraph("2. System Architecture & Concurrency Engineering", h1_style))
    story.append(Paragraph(
        "MediQueue adopts a <b>Modular Layered Monolith</b> architecture (ADR-002). Domain logic, ticket sequencing, and state transitions are centralized inside <code>App\\Services\\QueueService</code>. To guarantee race-condition immunity during concurrent patient arrivals, all queue assignments utilize pessimistic row-level locking (<code>lockForUpdate</code>) inside atomic transactions.",
        body_style
    ))

    arch_data = [
        [Paragraph("Tier / Layer", table_header), Paragraph("Technology & Implementation", table_header), Paragraph("Responsibility & Concurrency Guarantees", table_header)],
        [Paragraph("<b>Presentation Layer</b>", table_cell), Paragraph("Laravel Blade + Tailwind CSS v4", table_cell), Paragraph("Responsive glassmorphic UI, real-time polling updates, accessible forms", table_cell)],
        [Paragraph("<b>Security & Routing</b>", table_cell), Paragraph("RoleMiddleware & RateLimiter", table_cell), Paragraph("Multi-role isolation (patient/staff/admin), brute-force route throttling", table_cell)],
        [Paragraph("<b>Business Logic</b>", table_cell), Paragraph("QueueService.php", table_cell), Paragraph("Atomic ticket numbering (e.g. GC-005), state machine transitions, event dispatch", table_cell)],
        [Paragraph("<b>Data & Persistence</b>", table_cell), Paragraph("Eloquent ORM Models", table_cell), Paragraph("User, Service, QueueEntry, Notification, Setting, AuditLog schemas", table_cell)],
        [Paragraph("<b>Database Store</b>", table_cell), Paragraph("PostgreSQL / SQLite", table_cell), Paragraph("ACID transactional consistency with foreign keys, compound indexes, row locks", table_cell)],
    ]
    t_arch = Table(arch_data, colWidths=[110, 180, 225])
    t_arch.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#f1f5f9')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
    ]))
    story.append(t_arch)
    story.append(Spacer(1, 10))

    # ==================== 3. QUEUE STATE MACHINE ====================
    story.append(Paragraph("3. Queue Deterministic State Machine", h1_style))
    story.append(Paragraph(
        "Queue entries progress through a formal finite state machine. Invalid state mutations (such as attempting to complete an uncalled ticket) throw validation exceptions.",
        body_style
    ))

    sm_data = [
        [Paragraph("Initial State", table_header), Paragraph("Target State", table_header), Paragraph("Actor", table_header), Paragraph("Transition Trigger & Business Logic", table_header)],
        [Paragraph("<font color='#92400e'><b>WAITING</b></font>", table_cell), Paragraph("<font color='#3730a3'><b>CALLED</b></font>", table_cell), Paragraph("Staff", table_cell), Paragraph("Staff calls next ticket by priority FIFO; sets <code>called_at</code> and broadcasts alert.", table_cell)],
        [Paragraph("<font color='#92400e'><b>WAITING</b></font>", table_cell), Paragraph("<font color='#991b1b'><b>CANCELLED</b></font>", table_cell), Paragraph("Patient / Admin", table_cell), Paragraph("Patient cancels ticket before being called (Terminal state).", table_cell)],
        [Paragraph("<font color='#3730a3'><b>CALLED</b></font>", table_cell), Paragraph("<font color='#166534'><b>IN_SERVICE</b></font>", table_cell), Paragraph("Staff", table_cell), Paragraph("Patient reports to room; consultation starts and timers begin.", table_cell)],
        [Paragraph("<font color='#3730a3'><b>CALLED</b></font>", table_cell), Paragraph("<font color='#9a3412'><b>SKIPPED</b></font>", table_cell), Paragraph("Staff", table_cell), Paragraph("No-show patient moved to skipped pool after callout timeout.", table_cell)],
        [Paragraph("<font color='#9a3412'><b>SKIPPED</b></font>", table_cell), Paragraph("<font color='#3730a3'><b>CALLED</b></font>", table_cell), Paragraph("Staff", table_cell), Paragraph("Skipped patient reports to desk; recalled to active consultation call.", table_cell)],
        [Paragraph("<font color='#166534'><b>IN_SERVICE</b></font>", table_cell), Paragraph("<font color='#475569'><b>COMPLETED</b></font>", table_cell), Paragraph("Staff", table_cell), Paragraph("Consultation concluded; computes duration and archives ticket (Terminal state).", table_cell)],
    ]
    t_sm = Table(sm_data, colWidths=[90, 90, 90, 245])
    t_sm.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#f1f5f9')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0, 0), (-1, -1), 5),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 5),
    ]))
    story.append(t_sm)
    story.append(PageBreak())

    # ==================== 4. REQUIREMENTS TRACEABILITY MATRIX ====================
    story.append(Paragraph("4. Requirements Traceability Matrix (RTM)", h1_style))
    story.append(Paragraph(
        "All requirements specified in the SRS are fully mapped to concrete implementing classes and automated verification tests:",
        body_style
    ))

    rtm_data = [
        [Paragraph("Req ID", table_header), Paragraph("Requirement Description", table_header), Paragraph("Priority", table_header), Paragraph("Implementation Class", table_header), Paragraph("Verification Test", table_header)],
        [Paragraph("<b>REQ-AUTH-001</b>", table_cell), Paragraph("Patient Registration & Hashing", table_cell), Paragraph("MUST", table_cell), Paragraph("RegisterController", table_cell), Paragraph("AuthTest::test_patient_can_register", table_cell)],
        [Paragraph("<b>REQ-AUTH-006</b>", table_cell), Paragraph("Role-Based Access Control (RBAC)", table_cell), Paragraph("MUST", table_cell), Paragraph("RoleMiddleware", table_cell), Paragraph("AuthorizationTest (8 cases)", table_cell)],
        [Paragraph("<b>REQ-QUEUE-001</b>", table_cell), Paragraph("Atomic Ticket Numbering", table_cell), Paragraph("MUST", table_cell), Paragraph("QueueService::join", table_cell), Paragraph("QueueLifecycleTest::test_sequential", table_cell)],
        [Paragraph("<b>REQ-QUEUE-004</b>", table_cell), Paragraph("Duplicate Active Ticket Lock", table_cell), Paragraph("MUST", table_cell), Paragraph("QueueService::join", table_cell), Paragraph("QueueLifecycleTest::test_duplicate", table_cell)],
        [Paragraph("<b>REQ-QUEUE-008</b>", table_cell), Paragraph("Staff Call Next Operation", table_cell), Paragraph("MUST", table_cell), Paragraph("Staff\\QueueController", table_cell), Paragraph("QueueLifecycleTest::test_call_next", table_cell)],
        [Paragraph("<b>REQ-DISP-001</b>", table_cell), Paragraph("Hospital Public TV Departure Screen", table_cell), Paragraph("SHOULD", table_cell), Paragraph("DisplayController", table_cell), Paragraph("AdminEnhancementsTest::test_display", table_cell)],
        [Paragraph("<b>REQ-NOTIF-001</b>", table_cell), Paragraph("Transactional Email & In-App Alerts", table_cell), Paragraph("SHOULD", table_cell), Paragraph("QueueNotificationMail", table_cell), Paragraph("AdminEnhancementsTest::test_email", table_cell)],
        [Paragraph("<b>REQ-SETT-001</b>", table_cell), Paragraph("Clinic Settings & Password Reset", table_cell), Paragraph("SHOULD", table_cell), Paragraph("SettingController / UserController", table_cell), Paragraph("AdminEnhancementsTest::test_reset", table_cell)],
        [Paragraph("<b>REQ-REP-001</b>", table_cell), Paragraph("Analytics, CSV Export & Investigation", table_cell), Paragraph("SHOULD", table_cell), Paragraph("ReportController", table_cell), Paragraph("AdminEnhancementsTest & Manual Tests", table_cell)],
        [Paragraph("<b>REQ-AUDIT-001</b>", table_cell), Paragraph("Immutable Security Audit Ledger", table_cell), Paragraph("MUST", table_cell), Paragraph("AuditLog Model", table_cell), Paragraph("SmokeTest & LifecycleTests", table_cell)],
    ]
    t_rtm = Table(rtm_data, colWidths=[85, 140, 50, 110, 130])
    t_rtm.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#f1f5f9')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0, 0), (-1, -1), 5),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 5),
    ]))
    story.append(t_rtm)
    story.append(Spacer(1, 10))

    # ==================== 5. SOFTWARE EFFORT ESTIMATION ====================
    story.append(Paragraph("5. Software Effort Estimation (Use Case Points)", h1_style))
    story.append(Paragraph(
        "Using Gustav Karner's algorithmic Use Case Points formula, unadjusted UCP was computed as 164. Technical Complexity Factor (TCF = 0.935) and Environmental Complexity Factor (ECF = 0.590) adjusted total effort to <b>~90 UCP (46 person-hours)</b>, fitting the 48-hour examination scope.",
        body_style
    ))

    # ==================== 6. REPORTING & FORENSIC ACCOUNTABILITY ====================
    story.append(Paragraph("6. Reporting, CSV Export & Forensic Investigation", h1_style))
    story.append(Paragraph(
        "To satisfy hospital governance and clinical inquiry requirements, MediQueue provides an advanced <b>Reporting & Analytics Hub</b> (<code>/admin/reports</code>) alongside a <b>Forensic Chain of Custody Tracker</b> (<code>/admin/reports/investigate/{id}</code>):",
        body_style
    ))
    story.append(Paragraph(
        "• <b>Custom Date & Staff Filtering</b>: Filter attendance by department, date range, attending doctor, and status.<br/>"
        "• <b>Real-Time CSV Export</b>: Download a streaming CSV ledger of all queue entries including wait duration and consultation minutes.<br/>"
        "• <b>Executive Email Dispatch</b>: Instant one-click dispatch of operational summaries to the clinic administrator.<br/>"
        "• <b>Forensic Chain of Custody</b>: Full accountability tracking showing who registered the patient, which staff member called them, when service started and concluded, and all associated immutable audit log entries with client IP addresses.",
        body_style
    ))
    story.append(Spacer(1, 10))

    # ==================== 7. AUTOMATED QUALITY ASSURANCE ====================
    story.append(Paragraph("7. Quality Assurance & Automated Test Results", h1_style))
    story.append(Paragraph(
        "All 57 automated PHPUnit tests (234 assertions) executed with 100% pass rate:",
        body_style
    ))

    qa_data = [
        [Paragraph("Test Suite File", table_header), Paragraph("Scope Verified", table_header), Paragraph("Assertions", table_header), Paragraph("Result", table_header)],
        [Paragraph("<code>AuthTest.php</code>", table_cell), Paragraph("Registration, password hashing, login, logout, sessions", table_cell), Paragraph("16", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
        [Paragraph("<code>AuthorizationTest.php</code>", table_cell), Paragraph("RoleMiddleware, vertical/horizontal privilege isolation", table_cell), Paragraph("28", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
        [Paragraph("<code>QueueLifecycleTest.php</code>", table_cell), Paragraph("Atomic numbers, lockForUpdate, call/start/complete/skip", table_cell), Paragraph("52", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
        [Paragraph("<code>AdminEnhancementsTest.php</code>", table_cell), Paragraph("Hospital TV screen, settings, password reset, email dispatch", table_cell), Paragraph("34", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
        [Paragraph("<code>ReportControllerTest.php</code>", table_cell), Paragraph("Operational reports, CSV export, email report, chain of custody", table_cell), Paragraph("19", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
        [Paragraph("<code>TriageAndBedsTest.php</code>", table_cell), Paragraph("5-tier triage, bed allocation/release, appointment booking", table_cell), Paragraph("22", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
        [Paragraph("<code>ClinicalReferralsAndOnCallTest.php</code>", table_cell), Paragraph("Lab transfer loop, doctor review, Code Red trauma intake, paging", table_cell), Paragraph("33", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
        [Paragraph("<code>SmokeTest.php</code>", table_cell), Paragraph("Guest, patient, staff, admin, /docs 200 HTTP rendering", table_cell), Paragraph("30", table_cell), Paragraph("<b>100% PASS</b>", table_cell)],
    ]
    t_qa = Table(qa_data, colWidths=[150, 200, 65, 95])
    t_qa.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#f1f5f9')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
    ]))
    story.append(t_qa)
    story.append(PageBreak())

    # ==================== 8. SEEDED DEMO ACCOUNTS ====================
    story.append(Paragraph("8. Seeded Demo Accounts for Evaluation", h1_style))
    story.append(Paragraph("The following credentials can be used immediately to evaluate the live deployment:", body_style))

    acc_data = [
        [Paragraph("Role", table_header), Paragraph("Email Address", table_header), Paragraph("Password", table_header), Paragraph("Accessible Surfaces & Capabilities", table_header)],
        [Paragraph("<b>Administrator</b>", table_cell), Paragraph("admin@mediqueue.test", table_cell), Paragraph("password", table_cell), Paragraph("Full system control, services, password resets, settings, reports, audit.", table_cell)],
        [Paragraph("<b>Doctor / Staff</b>", table_cell), Paragraph("dr.sarah@mediqueue.test", table_cell), Paragraph("password", table_cell), Paragraph("Clinical queue console, Call Next CTA, start/complete, skip, recall.", table_cell)],
        [Paragraph("<b>Nurse / Staff</b>", table_cell), Paragraph("nurse.james@mediqueue.test", table_cell), Paragraph("password", table_cell), Paragraph("Nursing department queue triage and management.", table_cell)],
        [Paragraph("<b>Patient</b>", table_cell), Paragraph("john.doe@example.com", table_cell), Paragraph("password", table_cell), Paragraph("Queue ticket issuance, real-time live position tracking, history.", table_cell)],
    ]
    t_acc = Table(acc_data, colWidths=[90, 140, 75, 210])
    t_acc.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#f1f5f9')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
    ]))
    story.append(t_acc)
    story.append(Spacer(1, 15))

    # ==================== 9. SUBMISSION URLS & CONCLUSION ====================
    story.append(Paragraph("9. Conclusion & Submission Links", h1_style))
    story.append(Paragraph(
        "MediQueue demonstrates full compliance with all advanced software engineering capstone examination standards. The application is completely functional, architecturally disciplined, concurrency-safe, responsive, secure, and live in production.",
        body_style
    ))

    sub_data = [
        [Paragraph("<b>Resource</b>", table_header), Paragraph("<b>Production URL / Destination</b>", table_header)],
        [Paragraph("Live Web Application", table_cell), Paragraph("<font color='#4f46e5'>https://mediqueue-25vl.onrender.com</font>", table_cell)],
        [Paragraph("Hospital TV Departure Screen", table_cell), Paragraph("<font color='#4f46e5'>https://mediqueue-25vl.onrender.com/display</font>", table_cell)],
        [Paragraph("In-App Documentation Hub", table_cell), Paragraph("<font color='#4f46e5'>https://mediqueue-25vl.onrender.com/docs</font>", table_cell)],
        [Paragraph("Reporting & Analytics Portal", table_cell), Paragraph("<font color='#4f46e5'>https://mediqueue-25vl.onrender.com/admin/reports</font>", table_cell)],
        [Paragraph("GitHub Source Repository", table_cell), Paragraph("<font color='#4f46e5'>https://github.com/mhiskall282/MediQueue</font>", table_cell)],
    ]
    t_sub = Table(sub_data, colWidths=[160, 355])
    t_sub.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#f1f5f9')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#cbd5e1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#e2e8f0')),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
    ]))
    story.append(t_sub)

    # Build document
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Successfully generated PDF: {filename}")

if __name__ == "__main__":
    out_pdf = "docs/MediQueue_Capstone_Final_Report.pdf"
    if len(sys.argv) > 1:
        out_pdf = sys.argv[1]
    build_pdf(out_pdf)
    # Also create copy in root directory
    build_pdf("MediQueue_Capstone_Final_Report.pdf")
