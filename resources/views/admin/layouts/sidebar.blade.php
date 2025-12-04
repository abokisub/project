<!-- Sidebar -->
<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo Section -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 bg-white">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="KoboPoint Logo" class="h-8 w-auto">
                @elseif(file_exists(public_path('images/logo.svg')))
                    <img src="{{ asset('images/logo.svg') }}" alt="KoboPoint Logo" class="h-8 w-auto">
                @elseif(file_exists(public_path('images/logo.jpg')))
                    <img src="{{ asset('images/logo.jpg') }}" alt="KoboPoint Logo" class="h-8 w-auto">
                @else
                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-lg font-bold">K</span>
                    </div>
                @endif
                <span class="text-lg font-bold text-gray-900">KoboPoint</span>
            </a>
            <button id="sidebar-close" class="lg:hidden text-gray-500 hover:text-gray-700" onclick="toggleSidebar()">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto py-4 px-2">
            <ul class="space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" data-route="dashboard" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <i class="fas fa-home w-5 mr-3"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Users Management -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-users w-5 mr-3"></i>
                            <span>Users Management</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="users/all" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>All Users</a></li>
                        <li><a href="#" data-route="users/kyc" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>KYC Verification</a></li>
                        <li><a href="#" data-route="users/tier" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Tier Upgrades</a></li>
                        <li><a href="#" data-route="users/suspended" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Suspended Users</a></li>
                        <li><a href="#" data-route="users/wallets" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Wallets</a></li>
                        <li><a href="#" data-route="users/transactions" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>User Transactions</a></li>
                        <li><a href="#" data-route="users/devices" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Device Activity</a></li>
                    </ul>
                </li>

                <!-- Merchant Management -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-store w-5 mr-3"></i>
                            <span>Merchant Management</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="merchants/all" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>All Merchants</a></li>
                        <li><a href="#" data-route="merchants/applications" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Applications</a></li>
                        <li><a href="#" data-route="merchants/terminals" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>POS Terminals</a></li>
                        <li><a href="#" data-route="merchants/products" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Products</a></li>
                        <li><a href="#" data-route="merchants/transactions" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Merchant Transactions</a></li>
                        <li><a href="#" data-route="merchants/chargebacks" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Chargebacks</a></li>
                    </ul>
                </li>

                <!-- Transactions -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-exchange-alt w-5 mr-3"></i>
                            <span>Transactions</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="transactions/all" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>All Transactions</a></li>
                        <li><a href="#" data-route="transactions/wallet" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Wallet</a></li>
                        <li><a href="#" data-route="transactions/bank" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Bank Transfers</a></li>
                        <li><a href="#" data-route="transactions/qr" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>QR</a></li>
                        <li><a href="#" data-route="transactions/pos" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>POS</a></li>
                        <li><a href="#" data-route="transactions/failed" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Failed / Pending</a></li>
                        <li><a href="#" data-route="transactions/reversals" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Reversals</a></li>
                    </ul>
                </li>

                <!-- Wallet & Accounts -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-wallet w-5 mr-3"></i>
                            <span>Wallet & Accounts</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="wallets/user" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>User Wallets</a></li>
                        <li><a href="#" data-route="wallets/merchant" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Merchant Wallets</a></li>
                        <li><a href="#" data-route="wallets/settlements" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Settlements</a></li>
                        <li><a href="{{ route('admin.virtual-accounts') }}" data-route="wallets/virtual" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Virtual Accounts</a></li>
                        <li><a href="#" data-route="wallets/funding" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Funding Logs</a></li>
                    </ul>
                </li>

                <!-- KYC & Compliance -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt w-5 mr-3"></i>
                            <span>KYC & Compliance</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="kyc/bvn" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>BVN/NIN Logs</a></li>
                        <li><a href="#" data-route="kyc/aml" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>AML</a></li>
                        <li><a href="#" data-route="kyc/risk" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Risk Alerts</a></li>
                        <li><a href="#" data-route="kyc/sar" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>SAR Reports</a></li>
                    </ul>
                </li>

                <!-- Loans & Credit -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-hand-holding-usd w-5 mr-3"></i>
                            <span>Loans & Credit</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="loans/applications" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Loan Apps</a></li>
                        <li><a href="#" data-route="loans/active" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Active Loans</a></li>
                        <li><a href="#" data-route="loans/repayments" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Repayments</a></li>
                        <li><a href="#" data-route="loans/eligibility" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Eligibility</a></li>
                    </ul>
                </li>

                <!-- Savings / Thrift -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-piggy-bank w-5 mr-3"></i>
                            <span>Savings / Thrift</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="savings/thrift" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Thrift Groups</a></li>
                        <li><a href="#" data-route="savings/auto-debit" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Auto Debit</a></li>
                        <li><a href="#" data-route="savings/auto-credit" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Auto Credit</a></li>
                        <li><a href="#" data-route="savings/products" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Savings Products</a></li>
                    </ul>
                </li>

                <!-- Products & Services -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-box w-5 mr-3"></i>
                            <span>Products & Services</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="products/airtime" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Airtime/Data</a></li>
                        <li><a href="#" data-route="products/bills" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Bills</a></li>
                        <li><a href="#" data-route="products/providers" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Provider Logs</a></li>
                        <li><a href="#" data-route="products/pricing" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Price Settings</a></li>
                    </ul>
                </li>

                <!-- POS & Devices -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-terminal w-5 mr-3"></i>
                            <span>POS & Devices</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="pos/terminals" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Terminals</a></li>
                        <li><a href="#" data-route="pos/activation" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Activation</a></li>
                        <li><a href="#" data-route="pos/issues" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Issues</a></li>
                        <li><a href="#" data-route="pos/firmware" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Firmware</a></li>
                    </ul>
                </li>

                <!-- Support -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-headset w-5 mr-3"></i>
                            <span>Support</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="support/tickets" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Tickets</a></li>
                        <li><a href="#" data-route="support/chat" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Chat Logs</a></li>
                        <li><a href="#" data-route="support/feedback" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Feedback</a></li>
                        <li><a href="#" data-route="support/disputes" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Disputes</a></li>
                    </ul>
                </li>

                <!-- Communication -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-comments w-5 mr-3"></i>
                            <span>Communication</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="communication/sms" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>SMS</a></li>
                        <li><a href="#" data-route="communication/email" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Email</a></li>
                        <li><a href="#" data-route="communication/notifications" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Notifications</a></li>
                        <li><a href="#" data-route="communication/broadcast" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Broadcast</a></li>
                    </ul>
                </li>

                <!-- Finance & Accounting -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line w-5 mr-3"></i>
                            <span>Finance & Accounting</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="finance/revenue" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Revenue</a></li>
                        <li><a href="#" data-route="finance/settlements" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Settlements</a></li>
                        <li><a href="#" data-route="finance/recon" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Recon</a></li>
                        <li><a href="#" data-route="finance/ledger" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Ledger</a></li>
                    </ul>
                </li>

                <!-- Audit Logs -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-clipboard-list w-5 mr-3"></i>
                            <span>Audit Logs</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="audit/admin" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Admin Logs</a></li>
                        <li><a href="#" data-route="audit/api" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>API Logs</a></li>
                        <li><a href="#" data-route="audit/system" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>System Logs</a></li>
                    </ul>
                </li>

                <!-- Roles & Permissions -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-user-shield w-5 mr-3"></i>
                            <span>Roles & Permissions</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="roles/list" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Roles</a></li>
                        <li><a href="#" data-route="roles/staff" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Staff</a></li>
                        <li><a href="#" data-route="roles/access" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Access Control</a></li>
                    </ul>
                </li>

                <!-- API Management -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-code w-5 mr-3"></i>
                            <span>API Management</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="api/keys" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Keys</a></li>
                        <li><a href="#" data-route="api/webhooks" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Webhooks</a></li>
                        <li><a href="#" data-route="api/logs" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>API Logs</a></li>
                    </ul>
                </li>

                <!-- Settings -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-cog w-5 mr-3"></i>
                            <span>Settings</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="settings/providers" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Payment Providers</a></li>
                        <li><a href="#" data-route="settings/config" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>System Config</a></li>
                        <li><a href="#" data-route="settings/fees" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Fees & Charges</a></li>
                    </ul>
                </li>

                <!-- Developer Tools -->
                <li>
                    <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-100 hover:text-green-700 transition">
                        <div class="flex items-center">
                            <i class="fas fa-tools w-5 mr-3"></i>
                            <span>Developer Tools</span>
                        </div>
                        <i class="fas fa-chevron-right text-xs submenu-icon transition-transform"></i>
                    </button>
                    <ul class="submenu hidden mt-1 ml-4 space-y-1">
                        <li><a href="#" data-route="dev/database" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Database</a></li>
                        <li><a href="#" data-route="dev/queue" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Queue Monitor</a></li>
                        <li><a href="#" data-route="dev/cron" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 hover:text-green-700"><i class="fas fa-circle text-xs mr-3"></i>Cron Jobs</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>

