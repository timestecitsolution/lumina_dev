<ul>
    <!-- NAV ITEM - DASHBOARD COLLAPSE MENU-->
    @if (in_array('admin', user_roles())
    || $sidebarUserPermissions['view_overview_dashboard'] == 4
    || $sidebarUserPermissions['view_project_dashboard'] == 4
    || $sidebarUserPermissions['view_client_dashboard'] == 4
    || $sidebarUserPermissions['view_hr_dashboard'] == 4
    || $sidebarUserPermissions['view_ticket_dashboard'] == 4
    || $sidebarUserPermissions['view_finance_dashboard'] == 4
    )
        <x-menu-item icon="house" :text="__('app.menu.dashboard')">
            <x-slot name="iconPath">
                <path fill-rule="evenodd"
                      d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                <path fill-rule="evenodd"
                      d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
            </x-slot>
            <div class="accordionItemContent">
                <x-sub-menu-item :link="route('dashboard')"
                                 :text="__('app.menu.privateDashboard')" />
                <x-sub-menu-item :link="route('dashboard.advanced')"
                                 :text="__('app.menu.advanceDashboard')" />
            </div>
        </x-menu-item>
    @else
        <x-menu-item icon="house" :text="__('app.menu.dashboard')" :link="route('dashboard')">
            <x-slot name="iconPath">
                <path fill-rule="evenodd"
                      d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
                <path fill-rule="evenodd"
                      d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
            </x-slot>
        </x-menu-item>
    @endif

    <!-- NAV ITEM - MY CALENDAR -->
    @if (in_array('tasks', user_modules()) || in_array('events', user_modules()) || in_array('holidays', user_modules()) ||
                in_array('tickets', user_modules()) || in_array('leaves', user_modules()))
        <x-menu-item icon="calendar-range" :text="__('app.menu.myCalendar')" :link="route('my-calendar.index')" >
            <x-slot name="iconPath">
                <path d="M9 7a1 1 0 0 1 1-1h5v2h-5a1 1 0 0 1-1-1M1 9h4a1 1 0 0 1 0 2H1z"/>
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
            </x-slot>
        </x-menu-item>
    @endif

    <!-- NAV ITEM - HR COLLAPASE MENU -->
    @if (!in_array('client', user_roles()) && (in_array('leads', user_modules())) && (($sidebarUserPermissions['view_lead'] != 5 && $sidebarUserPermissions['view_lead'] != 'none') || ($sidebarUserPermissions['view_deals'] != 5 && $sidebarUserPermissions['view_deals'] != 'none')))
        <x-menu-item icon="person" :text="__('app.menu.crm')">
            <x-slot name="iconPath">
                <path d="M8 9.05a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                <path d="M1 1a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h.5a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h.5a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H6.707L6 1.293A1 1 0 0 0 5.293 1zm0 1h4.293L6 2.707A1 1 0 0 0 6.707 3H15v10h-.085a1.5 1.5 0 0 0-2.4-.63C11.885 11.223 10.554 10 8 10c-2.555 0-3.886 1.224-4.514 2.37a1.5 1.5 0 0 0-2.4.63H1z"/>
            </x-slot>
            @if ($sidebarUserPermissions['view_lead'] != 5 && $sidebarUserPermissions['view_lead'] != 'none')
            <div class="accordionItemContent ">
                <x-sub-menu-item :link="route('lead-contact.index')" :text="__('app.leadContact')" />
            </div>
            @endif
            @if ($sidebarUserPermissions['view_deals'] != 5 && $sidebarUserPermissions['view_deals'] != 'none')
                <div class="accordionItemContent ">
                    <x-sub-menu-item :link="route('deals.index')" :text="__('app.deal')" />
                </div>
            @endif

            @if (!in_array('client', user_roles()) && in_array('clients', user_modules()) && $sidebarUserPermissions['view_clients'] != 5 && $sidebarUserPermissions['view_clients'] != 'none')
                <div class="accordionItemContent ">
                    <x-sub-menu-item icon="building" :text="__('app.menu.clients')" :link="route('clients.index')"/>
                </div>
            @endif
        </x-menu-item>
    @endif


    

<!-- NAV ITEM - HR COLLAPASE MENU -->
    @if (!in_array('client', user_roles()) && (in_array('employees', user_modules()) || in_array('leaves', user_modules()) || in_array('attendance', user_modules()) || in_array('holidays', user_modules())) && ($sidebarUserPermissions['view_employees'] != 5 || $sidebarUserPermissions['view_leave'] != 5 || $sidebarUserPermissions['view_attendance'] != 5 || $sidebarUserPermissions['view_holiday'] != 5) && ($sidebarUserPermissions['view_employees'] != 'none' || $sidebarUserPermissions['view_leave'] != 'none' || $sidebarUserPermissions['view_attendance'] != 'none' || $sidebarUserPermissions['view_holiday'] != 'none' || $sidebarUserPermissions['view_shift_roster'] != 'none'))
        <x-menu-item icon="people" :text="__('app.menu.hrm')">
            <x-slot name="iconPath">
                <path
                    d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
            </x-slot>
            <div class="accordionItemContent">
                @if (in_array('employees', user_modules()) && $sidebarUserPermissions['view_employees'] != 5 && $sidebarUserPermissions['view_employees'] != 'none')
                    <x-sub-menu-item :link="route('employees.index')" :text="__('app.menu.employees')" />
                @endif
                @if (in_array('leaves', user_modules()) && $sidebarUserPermissions['view_leave'] != 5 && $sidebarUserPermissions['view_leave'] != 'none')
                    <x-sub-menu-item :link="route('leaves.index')" :text="__('app.menu.leaves')" />
                @endif
                @if (in_array('attendance', user_modules()) && isset($sidebarUserPermissions['view_shift_roster']) && $sidebarUserPermissions['view_shift_roster'] != 5 && $sidebarUserPermissions['view_shift_roster'] != 'none')
                    <x-sub-menu-item :link="route('shifts.index')" :text="__('app.menu.shiftRoster')" />
                @endif
                @if (in_array('attendance', user_modules()) && $sidebarUserPermissions['view_attendance'] != 5 && $sidebarUserPermissions['view_attendance'] != 'none')
                    <x-sub-menu-item :link="route('attendances.index')" :text="__('app.menu.attendance')" />
                @endif
                @if (in_array('holidays', user_modules()) && $sidebarUserPermissions['view_holiday'] != 5 && $sidebarUserPermissions['view_holiday'] != 'none')
                    <x-sub-menu-item :link="route('holidays.index')" :text="__('app.menu.holiday')" />
                @endif
                @if (isset($sidebarUserPermissions['view_designation']) && $sidebarUserPermissions['view_designation'] == 4 )
                    <x-sub-menu-item :link="route('designations.index')" :text="__('app.menu.designation')" />
                @endif
                @if (isset($sidebarUserPermissions['view_department']) && $sidebarUserPermissions['view_department'] == 4)
                    <x-sub-menu-item :link="route('departments.index')" :text="__('app.menu.department')" />
                @endif
                @if (isset($sidebarUserPermissions['view_appreciation']) && $sidebarUserPermissions['view_appreciation'] != 5)
                    <x-sub-menu-item :link="route('appreciations.index')" :text="__('app.menu.appreciation')" />
                @endif
                @if (isset($sidebarUserPermissions['view_appreciation'])  && $sidebarUserPermissions['view_appreciation'] == 5 && isset($sidebarUserPermissions['manage_award']) && $sidebarUserPermissions['manage_award'] == 4)
                    <x-sub-menu-item :link="route('awards.index')" :text="__('app.menu.appreciation')" />
                @endif
                <!-- NAV ITEM - CUSTOM MODULES  -->
                @foreach ($worksuitePlugins as $item)
                    @includeIf(strtolower($item) . '::sections.hr.sidebar')
                @endforeach
            </div>
        </x-menu-item>
    @endif


    @if (in_array(\Modules\Biometric\Entities\BiometricGlobalSetting::MODULE_NAME, user_modules()) && user()->permission('manage_biometric_settings') != 'none')
        <x-menu-item icon="fingerprint" :text="App::environment('demo') ? 'Biometric' : __('biometric::app.menu.biometric')" :addon="App::environment('demo')">
            <x-slot name="iconPath">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path
                        d="M48 256C48 141.1 141.1 48 256 48c63.1 0 119.6 28.1 157.8 72.5c8.6 10.1 23.8 11.2 33.8 2.6s11.2-23.8 2.6-33.8C403.3 34.6 333.7 0 256 0C114.6 0 0 114.6 0 256l0 40c0 13.3 10.7 24 24 24s24-10.7 24-24l0-40zm458.5-52.9c-2.7-13-15.5-21.3-28.4-18.5s-21.3 15.5-18.5 28.4c2.9 13.9 4.5 28.3 4.5 43.1l0 40c0 13.3 10.7 24 24 24s24-10.7 24-24l0-40c0-18.1-1.9-35.8-5.5-52.9zM256 80c-19 0-37.4 3-54.5 8.6c-15.2 5-18.7 23.7-8.3 35.9c7.1 8.3 18.8 10.8 29.4 7.9c10.6-2.9 21.8-4.4 33.4-4.4c70.7 0 128 57.3 128 128l0 24.9c0 25.2-1.5 50.3-4.4 75.3c-1.7 14.6 9.4 27.8 24.2 27.8c11.8 0 21.9-8.6 23.3-20.3c3.3-27.4 5-55 5-82.7l0-24.9c0-97.2-78.8-176-176-176zM150.7 148.7c-9.1-10.6-25.3-11.4-33.9-.4C93.7 178 80 215.4 80 256l0 24.9c0 24.2-2.6 48.4-7.8 71.9C68.8 368.4 80.1 384 96.1 384c10.5 0 19.9-7 22.2-17.3c6.4-28.1 9.7-56.8 9.7-85.8l0-24.9c0-27.2 8.5-52.4 22.9-73.1c7.2-10.4 8-24.6-.2-34.2zM256 160c-53 0-96 43-96 96l0 24.9c0 35.9-4.6 71.5-13.8 106.1c-3.8 14.3 6.7 29 21.5 29c9.5 0 17.9-6.2 20.4-15.4c10.5-39 15.9-79.2 15.9-119.7l0-24.9c0-28.7 23.3-52 52-52s52 23.3 52 52l0 24.9c0 36.3-3.5 72.4-10.4 107.9c-2.7 13.9 7.7 27.2 21.8 27.2c10.2 0 19-7 21-17c7.7-38.8 11.6-78.3 11.6-118.1l0-24.9c0-53-43-96-96-96zm24 96c0-13.3-10.7-24-24-24s-24 10.7-24 24l0 24.9c0 59.9-11 119.3-32.5 175.2l-5.9 15.3c-4.8 12.4 1.4 26.3 13.8 31s26.3-1.4 31-13.8l5.9-15.3C267.9 411.9 280 346.7 280 280.9l0-24.9z"/>
                </svg>
            </x-slot>

            <div class="accordionItemContent pb-2">
                    <x-sub-menu-item :link="route('biometric-devices.index')" :text="__('biometric::app.menu.devices')" />
                    <x-sub-menu-item :link="route('get-biometric-attendance')" :text="__('biometric::app.menu.attendance')" />
                    <x-sub-menu-item :link="route('biometric-employees.index')" :text="__('biometric::app.menu.deviceEmployees')" />
                    <x-sub-menu-item :link="route('biometric-devices.commands')" :text="__('biometric::app.menu.commands')" />
            </div>
        </x-menu-item>
    @endif

    @if (
    !in_array('client', user_roles()) &&
        in_array(\Modules\Letter\Entities\LetterSetting::MODULE_NAME, user_modules()) &&
        (user()->permission('view_letter') != 'none' || user()->permission('view_template') != 'none'))
        <x-menu-item icon="file" :text="__('letter::app.menu.letter')" :addon="App::environment('demo')">
            <x-slot name="iconPath">
                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                    <path
                        d="M64 112c-8.8 0-16 7.2-16 16v22.1L220.5 291.7c20.7 17 50.4 17 71.1 0L464 150.1V128c0-8.8-7.2-16-16-16H64zM48 212.2V384c0 8.8 7.2 16 16 16H448c8.8 0 16-7.2 16-16V212.2L322 328.8c-38.4 31.5-93.7 31.5-132 0L48 212.2zM0 128C0 92.7 28.7 64 64 64H448c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128z" />
                </svg>
            </x-slot>


            <div class="accordionItemContent">
                @if (user()->permission('view_letter') != 'none')
                    <x-sub-menu-item :link="route('letter.generate.index')" :text="__('letter::app.menu.generate')" />
                @endif
                @if (user()->permission('view_template') != 'none')
                    <x-sub-menu-item :link="route('letter.template.index')" :text="__('letter::app.menu.template')" />
                @endif
            </div>
        </x-menu-item>
    @endif


    @if (!in_array('client', user_roles()) && user()->permission('view_payroll') != 'none' && in_array(\Modules\Payroll\Entities\PayrollSetting::MODULE_NAME, user_modules()))
        <x-menu-item icon="wallet" :text="__('payroll::app.menu.payroll')" :addon="App::environment('demo')">
            <x-slot name="iconPath">
                <path
                    d="M0 3a2 2 0 0 1 2-2h13.5a.5.5 0 0 1 0 1H15v2a1 1 0 0 1 1 1v8.5a1.5 1.5 0 0 1-1.5 1.5h-12A2.5 2.5 0 0 1 0 12.5V3zm1 1.732V12.5A1.5 1.5 0 0 0 2.5 14h12a.5.5 0 0 0 .5-.5V5H2a1.99 1.99 0 0 1-1-.268zM1 3a1 1 0 0 0 1 1h12V2H2a1 1 0 0 0-1 1z"/>
            </x-slot>

            <div class="accordionItemContent pb-2">
                <x-sub-menu-item :link="route('payroll.index')" :text="__('payroll::app.menu.payroll')"/>
                <x-sub-menu-item :link="route('employee-salary.index')"
                                :text="__('payroll::app.menu.employeeSalary')"
                                :permission="user()->permission('manage_employee_salary') == 'all'"
                />
                <x-sub-menu-item :link="route('payroll-expenses.index')" :text="__('payroll::app.payrollExpenses')"/>
                <x-sub-menu-item :link="route('payroll-reports.index')" :text="__('app.menu.reports')"/>

            </div>
        </x-menu-item>
    @endif

    <x-menu-item icon="wallet" :link="route('overtime-requests.index')" :text="__('payroll::modules.payroll.overtimeRequest')">
        <x-slot name="iconPath">
            <path d="M8 3.5a.5.5 0 0 1 .5.5v4.25l3 1.75a.5.5 0 0 1-.5.866l-3.25-1.9A.5.5 0 0 1 7.5 8V4a.5.5 0 0 1 .5-.5z"/>
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm0-1A7 7 0 1 1 8 1a7 7 0 0 1 0 14z"/>
        </x-slot>
    </x-menu-item>

    @if (in_array('notices', user_modules()) && $sidebarUserPermissions['view_notice'] != 5 && $sidebarUserPermissions['view_notice'] != 'none')
        <x-menu-item icon="clipboard" :text="__('app.menu.noticeBoard')" :link="route('notices.index')">
            <x-slot name="iconPath">
                <path
                    d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" />
                <path
                    d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" />
            </x-slot>
        </x-menu-item>
    @endif


    @if (module_enabled('Performance') && in_array(\Modules\Performance\Entities\PerformanceSetting::MODULE_NAME, user_modules()))
    <x-menu-item icon="wallet" text="Performance" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                <path d="M3 12h2v8H3zm4-4h2v12H7zm4-4h2v16h-2zm4 6h2v10h-2zm4-2h2v12h-2z"/>
            </svg>
        </x-slot>

        <div class="accordionItemContent pb-2">
            <x-sub-menu-item :link="route('performance-dashboard.index')" :text="__('app.menu.dashboard')"/>
            <x-sub-menu-item :link="route('objectives.index')" :text="__('performance::app.objective')"/>
            <x-sub-menu-item :link="route('okr-scoring.index')" :text="__('performance::app.okrScoring')"/>
            <x-sub-menu-item :link="route('meetings.index')" :text="__('performance::app.oneOnOnemeetings')"/>
        </div>
    </x-menu-item>
    @endif

    @php
    $recruitViewDashboardPermission = user()->permission('view_dashboard');
    $recruitManageSkillPermission = user()->permission('manage_skill');
    $recruitViewJobPermission = user()->permission('view_job');
    $recruitViewJobApplicationPermission = user()->permission('view_job_application');
    $recruitViewInterviewPermission = user()->permission('view_interview_schedule');
    $recruitViewOfferLetterPermission = user()->permission('view_offer_letter');
    $recruitViewReportPermission = user()->permission('view_report');
@endphp

@if (in_array(\Modules\Recruit\Entities\RecruitSetting::MODULE_NAME, user_modules()))

    <x-menu-item icon="wallet" :text="__('recruit::app.menu.recruit')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1h-3zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
            <path
                d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0h-7zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492V2.5z"/>
        </x-slot>

        <div class="accordionItemContent">
            <x-sub-menu-item :link="route('recruit-dashboard.index')"
                             :text="__('recruit::app.menu.dashboard')"
                             :permission="($recruitViewDashboardPermission != 'none' && $recruitViewDashboardPermission != '')"
            />

            <x-sub-menu-item :link="route('jobs.index')"
                             :text="__('recruit::app.menu.job')"
                             :permission="($recruitViewJobPermission != 'none' && $recruitViewJobPermission != '')"
            />

            <x-sub-menu-item :link="route('job-applications.index')"
                             :text="__('recruit::app.menu.jobApplication')"
                             :permission="($recruitViewJobApplicationPermission != 'none' && $recruitViewJobApplicationPermission != '')"
            />

            <x-sub-menu-item :link="route('interview-schedule.index')"
                             :text="__('recruit::app.menu.interviewSchedule')"
                             :permission="($recruitViewInterviewPermission != 'none' && $recruitViewInterviewPermission != '')"
            />

            <x-sub-menu-item :link="route('job-offer-letter.index')"
                             :text="__('recruit::app.menu.offerletter')"
                             :permission="($recruitViewOfferLetterPermission != 'none' && $recruitViewOfferLetterPermission != '')"
            />


            <x-sub-menu-item :link="route('job-skills.index')"
                             :text="__('recruit::app.menu.skills')"
                             :permission="($recruitManageSkillPermission != 'none' && $recruitManageSkillPermission != '')"
            />

            <x-sub-menu-item :link="route('candidate-database.index')"
                             :text="__('recruit::app.menu.candidatedatabase')"
                             :permission="($recruitViewJobApplicationPermission != 'none' && $recruitViewJobApplicationPermission != '')"
            />

            <x-sub-menu-item :link="route('recruit-job-report.index')"
                             :text="__('recruit::app.menu.report')"
                             :permission="($recruitViewReportPermission != 'none' && $recruitViewReportPermission != '')"
            />

            <a class="d-block text-lightest f-14" target="_blank"
               href="{{ route('recruit', $company->hash) }}">@lang('recruit::app.menu.frontWebsite')</a>
        </div>
    </x-menu-item>

@endif






<!-- NAV ITEM - WORK COLLAPSE MENU -->
    @if ((in_array('contracts', user_modules()) || in_array('projects', user_modules()) || in_array('tasks', user_modules()) || in_array('timelogs', user_modules())) && ($sidebarUserPermissions['view_contract'] != 5 || $sidebarUserPermissions['view_projects'] != 5 || $sidebarUserPermissions['view_tasks'] != 5 || $sidebarUserPermissions['view_timelogs'] != 5) && ($sidebarUserPermissions['view_contract'] != 'none' || $sidebarUserPermissions['view_projects'] != 'none' || $sidebarUserPermissions['view_tasks'] != 'none' || $sidebarUserPermissions['view_timelogs'] != 'none'))
        <x-menu-item icon="briefcase" :text="__('app.menu.work')">
            <x-slot name="iconPath">
                <path
                    d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0zM1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5z" />
            </x-slot>
            <div class="accordionItemContent">
                @if (in_array('contracts', user_modules()) && $sidebarUserPermissions['view_contract'] != 5 && $sidebarUserPermissions['view_contract'] != 'none')
                    <x-sub-menu-item :link="route('contracts.index')" :text="__('app.menu.contracts')" />
                @endif
                @if (in_array('projects', user_modules()) && $sidebarUserPermissions['view_projects'] != 5 && $sidebarUserPermissions['view_projects'] != 'none')
                    <x-sub-menu-item :link="route('projects.index')" :text="__('app.menu.projects')" />
                @endif
                {{-- @if (!in_array('client', user_roles())) --}}
                @if (in_array('tasks', user_modules()) && $sidebarUserPermissions['view_tasks'] != 5 && $sidebarUserPermissions['view_tasks'] != 'none')
                    <x-sub-menu-item :link="route('tasks.index')" :text="__('app.menu.tasks')" />
                @endif
                @if (in_array('timelogs', user_modules()) && $sidebarUserPermissions['view_timelogs'] != 5 && $sidebarUserPermissions['view_timelogs'] != 'none')
                    <x-sub-menu-item :link="route('timelogs.index')" :text="__('app.menu.timeLogs')" />
                @endif
                {{-- @endif --}}
                <!-- NAV ITEM - CUSTOM MODULES  -->
                @foreach ($worksuitePlugins as $item)
                    @includeIf(strtolower($item) . '::sections.work.sidebar')
                @endforeach
            </div>
        </x-menu-item>
    @endif

<!-- NAV ITEM - FINANCE COLLAPASE MENU -->
    @if ((in_array('estimates', user_modules()) || in_array('invoices', user_modules()) || in_array('payments', user_modules()) || in_array('expenses', user_modules()) || in_array('bankaccount', user_modules())) && ($sidebarUserPermissions['view_estimates'] != 5 || $sidebarUserPermissions['view_invoices'] != 5 || $sidebarUserPermissions['view_payments'] != 5 || $sidebarUserPermissions['view_expenses'] != 5 || $sidebarUserPermissions['view_lead_proposals'] != 5 || $sidebarUserPermissions['view_bankaccount'] != 5) && ($sidebarUserPermissions['view_estimates'] != 'none' || $sidebarUserPermissions['view_invoices'] != 'none' || $sidebarUserPermissions['view_payments'] != 'none' || $sidebarUserPermissions['view_expenses'] != 'none' || $sidebarUserPermissions['view_lead_proposals'] != 'none' || $sidebarUserPermissions['view_bankaccount'] != 'none'))
        <x-menu-item icon="cash-coin" :active="($currentRouteName === 'payments.index')"
                     :text="__('app.menu.finance')">
            <x-slot name="iconPath">
                <path
                    d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z" />
            </x-slot>
            <div class="accordionItemContent">
                @if (in_array('leads', user_modules()) && $sidebarUserPermissions['view_lead_proposals'] != 5 && $sidebarUserPermissions['view_lead_proposals'] != 'none')
                    <x-sub-menu-item :link="route('proposals.index')" :text="__('app.menu.proposal')" />
                @endif
                @if (in_array('estimates', user_modules()) && $sidebarUserPermissions['view_estimates'] != 5 && $sidebarUserPermissions['view_estimates'] != 'none')
                    <x-sub-menu-item :link="route('estimates.index')" :text="__('app.menu.estimates')" />
                @endif
                @if (in_array('invoices', user_modules()) && $sidebarUserPermissions['view_invoices'] != 5 && $sidebarUserPermissions['view_invoices'] != 'none')
                    <x-sub-menu-item :link="route('invoices.index')" :text="__('app.menu.invoices')" />
                @endif
                @if (in_array('payments', user_modules()) && $sidebarUserPermissions['view_payments'] != 5 && $sidebarUserPermissions['view_payments'] != 'none')
                    <x-sub-menu-item :link="route('payments.index')" :text="__('app.menu.payments')" />
                @endif
                @if (in_array('invoices', user_modules()) && $sidebarUserPermissions['view_invoices'] != 5 && $sidebarUserPermissions['view_invoices'] != 'none')
                    <x-sub-menu-item :link="route('creditnotes.index')"
                                     :text="__('app.menu.credit-note')" />
                @endif

                @if (in_array('expenses', user_modules()) && $sidebarUserPermissions['view_expenses'] != 5 && $sidebarUserPermissions['view_expenses'] != 'none')
                    <x-sub-menu-item :link="route('expenses.index')" :text="__('app.menu.expenses')" />
                @endif

                @if (in_array('bankaccount', user_modules()) && $sidebarUserPermissions['view_bankaccount'] != 5 && $sidebarUserPermissions['view_bankaccount'] != 'none')
                    <x-sub-menu-item :link="route('bankaccounts.index')" :text="__('app.menu.bankaccount')" />
                @endif


                <x-sub-menu-item :link="route('investors.index')" :text="__('app.menu.investor')" />
                <x-sub-menu-item :link="route('investments.index')" :text="__('app.menu.investment')" />
                <x-sub-menu-item :link="route('loan.index')" :text="__('app.menu.loan')" />
                
                <!-- NAV ITEM - CUSTOM MODULES  -->
                @foreach ($worksuitePlugins as $item)
                    @includeIf(strtolower($item) . '::sections.finance.sidebar')
                @endforeach
            </div>
        </x-menu-item>
    @endif




    <x-menu-item icon="gear" :link="route('requisitions.index')"
                            :text="__('purchase::app.menu.requisition')">
        <x-slot name="iconPath">
            <path
                d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
            <path
                d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
        </x-slot>
    </x-menu-item>

    



    @php
    $purchaseViewVendorPermission = user()->permission('view_vendor');
    $purchaseViewOrderPermission = user()->permission('view_purchase_order');
    $purchaseViewBillPermission = user()->permission('view_bill');
    $purchaseViewCreditPermission = user()->permission('view_vendor_credit');
    $purchaseViewInventoryPermission = user()->permission('view_inventory');
    $purchaseViewOrderReportPermission = user()->permission('view_order_report');
    $purchaseViewPaymentPermission = user()->permission('view_vendor_payment');
@endphp
@if (in_array(\Modules\Purchase\Entities\PurchaseManagementSetting::MODULE_NAME, user_modules()) && ($purchaseViewVendorPermission != 'none' || $purchaseViewOrderPermission != 'none' || $purchaseViewBillPermission != 'none'
|| $purchaseViewCreditPermission != 'none' || $purchaseViewInventoryPermission != 'none' || $purchaseViewOrderReportPermission != 'none' || $purchaseViewPaymentPermission != 'none'))

    <x-menu-item icon="wallet" :text="__('purchase::app.menu.purchase')" :addon="App::environment('demo')">
        <x-slot name="iconPath">
            <path d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z"/>
            <path d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z"/>
        </x-slot>

        <div class="accordionItemContent pb-2">

            <!-- NAV ITEM - VENDORS -->
            <x-sub-menu-item :link="route('vendors.index')"
                            :text="__('purchase::app.menu.vendor')"
                            :permission="($purchaseViewVendorPermission != 'none' && $purchaseViewVendorPermission != '')"
            />

            <!-- NAV ITEM - PRODUCTS -->
            @if (in_array('products', user_modules()) && $sidebarUserPermissions['view_product'] != 5 && $sidebarUserPermissions['view_product'] != 'none')
               <x-sub-menu-item :link="route('purchase-products.index')" :text="__('purchase::app.menu.products')" />
            @endif


            

            <!-- NAV ITEM - ORDERS -->
            <x-sub-menu-item :link="route('purchase-order.index')"
                            :text="__('purchase::app.menu.purchaseOrder')"
                            :permission="($purchaseViewOrderPermission != 'none' && $purchaseViewOrderPermission != '')"
            />

            <!-- NAV ITEM - BILLS -->
            <x-sub-menu-item :link="route('bills.index')"
                            :text="__('purchase::app.menu.bills')"
                            :permission="($purchaseViewBillPermission != 'none' && $purchaseViewBillPermission != '')"
            />

            <!-- NAV ITEM - PAYMENTS -->
            <x-sub-menu-item :link="route('vendor-payments.index')"
                            :text="__('purchase::app.purchaseOrder.vendorPayments')"
                            :permission="($purchaseViewPaymentPermission != 'none' && $purchaseViewPaymentPermission != '')"
            />

            <x-sub-menu-item :link="route('vendor-credits.index')"
                            :text="__('purchase::app.menu.vendorCredits')"
                            :permission="($purchaseViewCreditPermission != 'none' && $purchaseViewCreditPermission != '')"
            />

            <!-- NAV ITEM - INVENTORY -->
            <x-sub-menu-item :link="route('purchase-inventory.index')" :text="__('purchase::app.menu.inventory')"
            :permission="($purchaseViewInventoryPermission != 'none' && $purchaseViewInventoryPermission != '')"
            />

            <!-- NAV ITEM - REPORTS -->
            <x-sub-menu-item :link="route('reports.index')" :text="__('purchase::app.menu.reports')"
            :permission="($purchaseViewOrderReportPermission != 'none' && $purchaseViewOrderReportPermission != '')"
            />

        </div>

    </x-menu-item>

@endif


<!-- NAV ITEM - PRODUCTS -->
@if (!in_array('purchase', user_modules()) && in_array('products', user_modules()) && $sidebarUserPermissions['view_product'] != 5 && $sidebarUserPermissions['view_product'] != 'none')
        <x-menu-item icon="basket" :text="__('app.menu.products')" :link="route('products.index')">
            <x-slot name="iconPath">
                <path
                    d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1v4.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V9a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1.217L5.07 1.243a.5.5 0 0 1 .686-.172zM2 9v4.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9H2zM1 7v1h14V7H1zm3 3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 4 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 6 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z" />
            </x-slot>
        </x-menu-item>
@endif

<!-- NAV ITEM - PRODUCTS -->
    @if (in_array('orders', user_modules()) && $sidebarUserPermissions['view_order'] != 5 && $sidebarUserPermissions['view_order'] != 'none')
        <x-menu-item icon="cart3" :text="__('app.menu.orders')" :link="route('orders.index')">
            <x-slot name="iconPath">
                <path
                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </x-slot>
        </x-menu-item>
    @endif
    
<!-- NAV ITEM - TICKETS -->
    @if (in_array('tickets', user_modules()) && $sidebarUserPermissions['view_tickets'] != 5 && $sidebarUserPermissions['view_tickets'] != 'none')
        <!-- <x-menu-item icon="headset" :text="__('app.menu.tickets')" :link="route('tickets.index')">
            <x-slot name="iconPath">
                <path
                    d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5z" />
            </x-slot>
        </x-menu-item> -->
    @endif

<!-- NAV ITEM - EVENTS -->
    @if (in_array('events', user_modules()) && $sidebarUserPermissions['view_events'] != 5 && $sidebarUserPermissions['view_events'] != 'none')
       <!--  <x-menu-item icon="calendar-event" :text="__('app.menu.events')" :link="route('events.index')">
            <x-slot name="iconPath">
                <path
                    d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                <path
                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
            </x-slot>
        </x-menu-item> -->
    @endif

<!-- NAV ITEM - MESSAGES -->
    @if (in_array('messages', user_modules()))
        @if ((message_setting()->allow_client_admin == 'yes' || message_setting()->allow_client_employee == 'yes') && in_array('client', user_roles()))
           <!--  <x-menu-item class="message-menu" icon="chat-left-text" :text="__('app.menu.messages')"
                         :count="$unreadMessagesCount" :link="route('messages.index')">
                <x-slot name="iconPath">
                    <path
                        d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                    <path
                        d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z" />
                </x-slot>
            </x-menu-item> -->
        @elseif (in_array('employee', user_roles()))
           <!--  <x-menu-item class="message-menu" icon="chat-left-text" :text="__('app.menu.messages')"
                         :link="route('messages.index')" :count="$unreadMessagesCount">
                <x-slot name="iconPath">
                    <path
                        d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                    <path
                        d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6zm0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z" />
                </x-slot>
            </x-menu-item> -->
        @endif
    @endif

<!-- NAV ITEM - GDPR -->
    @if ((in_array('admin', user_roles()) || in_array('client', user_roles())) && $gdpr->enable_gdpr == 1)
        <!-- <x-menu-item icon="lock" :text="__('app.menu.gdpr')" :link="route('gdpr.index')">
            <x-slot name="iconPath">
                <path
                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z" />
            </x-slot>
        </x-menu-item> -->
    @endif

<!-- NAV ITEM - NOTICES -->


<!-- Knowledge base -->
    @if (in_array('knowledgebase', user_modules()) && isset($sidebarUserPermissions['view_knowledgebase']) && $sidebarUserPermissions['view_knowledgebase'] != 5)
      <!--   <x-menu-item icon="note" :text="__('app.menu.knowledgebase')" :link="route('knowledgebase.index')">
            <x-slot name="iconPath">
                <g xmlns="http://www.w3.org/2000/svg" id="surface1">
                    <path
                        d="M 11.824219 0.21875 C 11.722656 0.0820312 11.5625 0 11.390625 0 L 2.277344 0 C 1.394531 0 0.675781 0.714844 0.675781 1.601562 L 0.675781 14.398438 C 0.675781 15.285156 1.394531 16 2.277344 16 L 13.71875 16 C 14.601562 16 15.320312 15.285156 15.320312 14.398438 L 15.320312 5.199219 C 15.320312 5.085938 15.285156 4.976562 15.21875 4.886719 Z M 11.121094 1.066406 L 14.253906 5.375 L 14.253906 14.398438 C 14.253906 14.695312 14.015625 14.933594 13.71875 14.933594 L 2.277344 14.933594 C 1.984375 14.933594 1.742188 14.695312 1.742188 14.398438 L 1.742188 1.601562 C 1.742188 1.304688 1.984375 1.066406 2.277344 1.066406 Z M 11.121094 1.066406 " />
                    <path
                        d="M 3.246094 4.460938 L 8 4.460938 C 8.292969 4.460938 8.53125 4.222656 8.53125 3.925781 C 8.53125 3.632812 8.292969 3.394531 8 3.394531 L 3.246094 3.394531 C 2.953125 3.394531 2.714844 3.632812 2.714844 3.925781 C 2.714844 4.222656 2.953125 4.460938 3.246094 4.460938 Z M 3.246094 4.460938 " />
                    <path
                        d="M 14.785156 5.429688 L 11.925781 5.429688 L 11.925781 0.535156 C 11.925781 0.238281 11.6875 0 11.390625 0 C 11.097656 0 10.859375 0.238281 10.859375 0.535156 L 10.859375 5.964844 C 10.859375 6.257812 11.097656 6.496094 11.390625 6.496094 L 14.785156 6.496094 C 15.082031 6.496094 15.320312 6.257812 15.320312 5.964844 C 15.320312 5.667969 15.082031 5.429688 14.785156 5.429688 Z M 14.785156 5.429688 " />
                    <path
                        d="M 3.246094 7.855469 L 8 7.855469 C 8.292969 7.855469 8.53125 7.617188 8.53125 7.320312 C 8.53125 7.027344 8.292969 6.789062 8 6.789062 L 3.246094 6.789062 C 2.953125 6.789062 2.714844 7.027344 2.714844 7.320312 C 2.714844 7.617188 2.953125 7.855469 3.246094 7.855469 Z M 3.246094 7.855469 " />
                    <path
                        d="M 3.246094 10.910156 L 10.035156 10.910156 C 10.328125 10.910156 10.566406 10.671875 10.566406 10.375 C 10.566406 10.082031 10.328125 9.84375 10.035156 9.84375 L 3.246094 9.84375 C 2.953125 9.84375 2.714844 10.082031 2.714844 10.375 C 2.714844 10.671875 2.953125 10.910156 3.246094 10.910156 Z M 3.246094 10.910156 " />
                </g>

            </x-slot>
        </x-menu-item> -->
    @endif
<!-- Knowledge base -->

<x-menu-item icon="cash-coin" :text="__('app.menu.labour')">
        <x-slot name="iconPath">
            <path d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z"/>
            <path d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z"/>
        </x-slot>

        <div class="accordionItemContent pb-2">

            <!-- NAV ITEM - VENDORS -->
            <x-sub-menu-item :link="route('contractor-types.index')" :text="__('app.menu.contractor_type')"/>
            <x-sub-menu-item :link="route('contractors.index')" :text="__('app.menu.contractor')"/>
            <x-sub-menu-item :link="route('sections.index')" :text="__('app.menu.section')"/>
            <x-sub-menu-item :link="route('steps.index')" :text="__('app.menu.step')"/>
            <x-sub-menu-item :link="route('deeds.index')" :text="__('app.menu.deeds')"/>

        </div>

    </x-menu-item>



    <!-- NAV ITEM - NOTES -->
    @if (in_array('client', user_roles()) && $sidebarUserPermissions['view_client_note'] != 5)
        <x-menu-item icon="journal-text" :text="__('app.menu.notes')" :link="route('client-notes.index')">
            <x-slot name="iconPath">
                <path
                    d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z" />
                <path
                    d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z" />
                <path
                    d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z" />
            </x-slot>
        </x-menu-item>
    @endif

<!-- NAV ITEM - CUSTOM MODULES  -->
    @foreach ($worksuitePlugins as $item)
        @includeIf(strtolower($item) . '::sections.sidebar')
    @endforeach

<!-- NAV ITEM - NOTICES -->
    @if (in_array('reports', user_modules()) && ($sidebarUserPermissions['view_task_report'] == 4 || $sidebarUserPermissions['view_time_log_report'] == 4 || (isset($sidebarUserPermissions['view_expense_report']) && $sidebarUserPermissions['view_expense_report'] == 4) || $sidebarUserPermissions['view_finance_report'] != 5 || $sidebarUserPermissions['view_income_expense_report'] == 4 || $sidebarUserPermissions['view_leave_report'] != 5 || $sidebarUserPermissions['view_attendance_report'] == 4 || $sidebarUserPermissions['view_lead_report'] == 4 || $sidebarUserPermissions['view_sales_report'] == 4) && ($sidebarUserPermissions['view_task_report'] != 'none' || $sidebarUserPermissions['view_time_log_report'] != 'none' || $sidebarUserPermissions['view_finance_report'] != 'none' || $sidebarUserPermissions['view_income_expense_report'] != 'none' || $sidebarUserPermissions['view_leave_report'] != 'none' || $sidebarUserPermissions['view_attendance_report'] != 'none' || $sidebarUserPermissions['view_lead_report'] != 'none' || $sidebarUserPermissions['view_sales_report'] != 'none' || (isset($sidebarUserPermissions['view_expense_report']) && $sidebarUserPermissions['view_expense_report'] != 'none')))
        <x-menu-item icon="graph-up" :text="__('app.menu.reports')">
            <x-slot name="iconPath">
                <path
                    d="M7.5 1.018a7 7 0 0 0-4.79 11.566L7.5 7.793V1.018zm1 0V7.5h6.482A7.001 7.001 0 0 0 8.5 1.018zM14.982 8.5H8.207l-4.79 4.79A7 7 0 0 0 14.982 8.5zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z" />
            </x-slot>

            <div class="accordionItemContent">
                @if ($sidebarUserPermissions['view_task_report'] == 4 && $sidebarUserPermissions['view_task_report'] != 'none' && in_array('tasks', user_modules()))
                    <x-sub-menu-item :link="route('task-report.index')" :text="__('app.menu.taskReport')" />
                @endif

                @if ($sidebarUserPermissions['view_time_log_report'] == 4 && $sidebarUserPermissions['view_time_log_report'] != 'none' && in_array('timelogs', user_modules()))
                    <x-sub-menu-item :link="route('time-log-report.index')"
                                     :text="__('app.menu.timeLogReport')" />
                @endif

               @if ($sidebarUserPermissions['view_time_log_report'] == 4 && $sidebarUserPermissions['view_time_log_report'] != 'none' && in_array('timelogs', user_modules()))
                    <x-sub-menu-item :link="route('time-log-weekly-report.index')"
                                     :text="__('app.menu.weeklyTimehsheet')" />
                @endif

                @if ($sidebarUserPermissions['view_finance_report'] != 5 && $sidebarUserPermissions['view_finance_report'] != 'none' && in_array('payments', user_modules()))
                    <x-sub-menu-item :link="route('finance-report.index')"
                                     :text="__('app.menu.financeReport')" />
                @endif

                @if ($sidebarUserPermissions['view_income_expense_report'] == 4 && $sidebarUserPermissions['view_income_expense_report'] != 'none' && in_array('expenses', user_modules()))
                    <x-sub-menu-item :link="route('income-expense-report.index')"
                                     :text="__('app.menu.incomeVsExpenseReport')" />
                @endif

                @if ($sidebarUserPermissions['view_leave_report'] != 5 && $sidebarUserPermissions['view_leave_report'] != 'none' && in_array('leaves', user_modules()))
                    <x-sub-menu-item :link="route('leave-report.leave_quota')"
                                     :text="__('app.menu.leaveReport')" />
                @endif

                @if ($sidebarUserPermissions['view_attendance_report'] == 4 && $sidebarUserPermissions['view_attendance_report'] != 'none' && in_array('attendance', user_modules()))
                    <x-sub-menu-item :link="route('attendance-report.index')"
                                     :text="__('app.menu.attendanceReport')" />
                @endif
                @if (isset($sidebarUserPermissions['view_expense_report']) && $sidebarUserPermissions['view_expense_report'] == 4 && $sidebarUserPermissions['view_expense_report'] != 'none' && in_array('expenses', user_modules()))
                    <x-sub-menu-item :link="route('expense-report.index')"
                                     :text="__('app.menu.expenseReport')" />
                @endif
                @if (isset($sidebarUserPermissions['view_lead_report']) && $sidebarUserPermissions['view_lead_report'] == 4 && $sidebarUserPermissions['view_lead_report'] != 'none' && in_array('leads', user_modules()))
                    <x-sub-menu-item :link="route('lead-report.index')"
                                     :text="__('app.menu.leadReport')" />

                @endif
                @if (isset($sidebarUserPermissions['view_sales_report']) && $sidebarUserPermissions['view_sales_report'] == 4 && $sidebarUserPermissions['view_sales_report'] != 'none' && in_array('invoices', user_modules()))
                    <x-sub-menu-item :link="route('sales-report.index')"
                                     :text="__('app.menu.salesReport')" />
                @endif

                <x-sub-menu-item :link="route('accounting-reports.ledger')" :text="__('app.menu.ledger')" />
                <x-sub-menu-item :link="route('accounting-reports.ledger')" :text="__('app.menu.ledger')" />
                <x-sub-menu-item :link="route('accounting-reports.trial-balance')" :text="__('app.menu.trial-balance')" />
                <x-sub-menu-item :link="route('accounting-reports.income-statement')" :text="__('app.menu.income-statement')" />
                <x-sub-menu-item :link="route('accounting-reports.balance-sheet')" :text="__('app.menu.balance-sheet')" />
                <x-sub-menu-item :link="route('accounting-reports.cash-book')" :text="__('app.menu.cash-book')" />
                
            </div>
        </x-menu-item>
@endif

<!-- NAV ITEM - CUSTOM LINK -->

    @php
    $role = user()->role->last();
    @endphp

    @foreach ($customLink as $item)
        @if((in_array($role->role_id, json_decode($item->can_be_viewed_by)) || in_array('admin', user_roles())) && $item->status == 'active')
            <li>
                <a class="nav-item text-lightest f-15 sidebar-text-color" href={{$item->url}} target="_blank"
                title={{$item->link_title}}>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-link" viewBox="0 0 16 16">
                        <path d="M6.354 5.5H4a3 3 0 0 0 0 6h3a3 3 0 0 0 2.83-4H9c-.086 0-.17.01-.25.031A2 2 0 0 1 7 10.5H4a2 2 0 1 1 0-4h1.535c.218-.376.495-.714.82-1z"/>
                        <path d="M9 5.5a3 3 0 0 0-2.83 4h1.098A2 2 0 0 1 9 6.5h3a2 2 0 1 1 0 4h-1.535a4.02 4.02 0 0 1-.82 1H12a3 3 0 1 0 0-6H9z"/>
                    </svg>
                    <span class="pl-3">{{$item->link_title}}</span>
                </a>
            </li>
        @endif
    @endforeach

<!-- NAV ITEM - REPORTS COLLAPASE MENU -->
    <!-- NAV ITEM - SETTINGS -->
    
    <x-menu-item icon="gear" :text="__('app.menu.validationSettings')"
                 :link="(route('validation-settings.index'))">
        <x-slot name="iconPath">
            <path
                d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
            <path
                d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
        </x-slot>
    </x-menu-item>

    <x-menu-item icon="gear" :text="__('app.menu.settings')"
                 :link="($sidebarUserPermissions['manage_company_setting'] == 4 ? route('company-settings.index') : route('profile-settings.index'))">
        <x-slot name="iconPath">
            <path
                d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
            <path
                d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
        </x-slot>
    </x-menu-item>


</ul>
