<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'EduNexUs') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <div class="min-h-screen flex">

        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-col lg:w-72 bg-white border-r border-slate-200">

            <!-- Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-100">
                <div>
                    <h1 class="text-2xl font-bold text-teal-700">
                        EduNexUs
                    </h1>

                    <p class="text-xs text-slate-500 mt-1">
                        Programmable Cooperative Assistance
                    </p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl bg-teal-50 text-teal-700 font-medium">
                    <span>📊</span>
                    <span>Dashboard</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 transition">
                    <span>🛟</span>
                    <span>Programs</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 transition">
                    <span>📄</span>
                    <span>Requests</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 transition">
                    <span>✅</span>
                    <span>Claims</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 transition">
                    <span>💳</span>
                    <span>Settlements</span>
                </a>

                <a href="#"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-100 transition">
                    <span>⛓️</span>
                    <span>Blockchain Logs</span>
                </a>

            </nav>

        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">

            <!-- Top Navbar -->
            <header class="h-20 bg-white border-b border-slate-200 px-8 flex items-center justify-between">

                <div>
                    <h2 class="text-xl font-semibold text-slate-800">
                        Dashboard Overview
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Welcome to EduNexUs Administration Portal
                    </p>
                </div>

                <div class="flex items-center gap-4">

                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-700">
                            {{ Auth::user()->name }}
                        </p>

                        <p class="text-xs text-slate-500 capitalize">
                            {{ Auth::user()->role }}
                        </p>
                    </div>

                    <div class="w-11 h-11 rounded-full bg-teal-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                </div>

            </header>

            <!-- Page Content -->
            <main class="flex-1 p-8">

                <!-- Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-sm text-slate-500">
                            Total Assistance
                        </p>

                        <h3 class="text-3xl font-bold mt-3 text-slate-800">
                            ₱125,000
                        </h3>

                        <p class="text-sm text-emerald-600 mt-2">
                            +12% this month
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-sm text-slate-500">
                            Approved Claims
                        </p>

                        <h3 class="text-3xl font-bold mt-3 text-slate-800">
                            84
                        </h3>

                        <p class="text-sm text-emerald-600 mt-2">
                            Verified successfully
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-sm text-slate-500">
                            Pending Settlements
                        </p>

                        <h3 class="text-3xl font-bold mt-3 text-slate-800">
                            16
                        </h3>

                        <p class="text-sm text-yellow-600 mt-2">
                            Awaiting processing
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                        <p class="text-sm text-slate-500">
                            Blockchain Verifications
                        </p>

                        <h3 class="text-3xl font-bold mt-3 text-slate-800">
                            142
                        </h3>

                        <p class="text-sm text-cyan-600 mt-2">
                            Morph confirmed
                        </p>
                    </div>

                </div>

                <!-- Recent Activity -->
                <div class="mt-8 bg-white rounded-2xl border border-slate-200 shadow-sm">

                    <div class="px-6 py-5 border-b border-slate-100">
                        <h3 class="text-lg font-semibold text-slate-800">
                            Recent Activity
                        </h3>
                    </div>

                    <div class="overflow-x-auto">

                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-4 text-left">Reference</th>
                                    <th class="px-6 py-4 text-left">Member</th>
                                    <th class="px-6 py-4 text-left">Program</th>
                                    <th class="px-6 py-4 text-left">Status</th>
                                    <th class="px-6 py-4 text-left">Amount</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                <tr>
                                    <td class="px-6 py-4 font-medium text-slate-700">
                                        EDU-2026-001
                                    </td>

                                    <td class="px-6 py-4">
                                        Maria Santos
                                    </td>

                                    <td class="px-6 py-4">
                                        Education Assistance
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-semibold">
                                            Approved
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 font-semibold">
                                        ₱2,500
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </main>

        </div>

    </div>
</body>
</html>