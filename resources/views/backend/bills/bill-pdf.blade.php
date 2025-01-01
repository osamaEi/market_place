<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $bill->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 12px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .logo {
            width: 60px;
        }
        .invoice-details {
            margin-bottom: 15px;
        }
        .row {
            display: flex;
            margin-bottom: 10px;
        }
        .col {
            flex: 1;
            padding-right: 10px;
        }
        .main-content {
            display: flex;
            gap: 20px;
        }
        .left-section {
            flex: 2;
        }
        .right-section {
            flex: 1;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .table th,
        .table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: right;
            font-size: 11px;
        }
        .table th:first-child,
        .table td:first-child {
            text-align: left;
        }
        .totals {
            width: 100%;
            margin-left: auto;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        .details-card {
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 4px;
            padding: 12px;
        }
        .badge {
            background: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .text-gray {
            color: #6c757d;
        }
        .text-small {
            font-size: 10px;
        }
        .fw-bold {
            font-weight: bold;
        }
        .bullet {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #dc3545;
            border-radius: 50%;
            margin-right: 4px;
        }
        h1 {
            font-size: 16px;
            margin: 0;
        }
        h6 {
            font-size: 12px;
            margin: 10px 0;
        }
        .details-section {
            margin-bottom: 10px;
        }
        .details-row {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('storage/' . $configuration->logo) }}" alt="Logo" class="logo">
            <h1>Invoice #{{ $bill->id }}</h1>
        </div>

        <div class="invoice-details">
            <div class="row">
                <div class="col">
                    <div class="text-gray text-small">Start Date:</div>
                    <div class="fw-bold">{{ $bill->subscription_start_date }}</div>
                </div>
                <div class="col">
                    <div class="text-gray text-small">End Date:</div>
                    <div class="fw-bold">{{ $bill->subscription_end_date }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="text-gray text-small">Issue For:</div>
                    <div class="fw-bold">{{ $bill->customer->name }}</div>
                    <div class="text-gray">{{ $bill->customer->address }}</div>
                </div>
                <div class="col">
                    <div class="text-gray text-small">Issued By:</div>
                    <div class="fw-bold">HyperSale</div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="left-section">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Normal Ads</th>
                            <th>Banner</th>
                            <th>Commercial</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span class="bullet"></span>
                                {{ $bill->remaining_ads_normal }}
                            </td>
                            <td>{{ $bill->remaining_ads_banner }}</td>
                            <td>{{ $bill->remaining_ads_commercial }}</td>
                            <td class="fw-bold">${{ number_format($bill->amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="totals">
                    <div class="total-row">
                        <div class="text-gray text-small">Subtotal:</div>
                        <div class="fw-bold">${{ number_format($bill->amount, 2) }}</div>
                    </div>
                    <div class="total-row">
                        <div class="text-gray text-small">VAT 0%:</div>
                        <div class="fw-bold">$0.00</div>
                    </div>
                    <div class="total-row">
                        <div class="text-gray text-small">Subtotal + VAT:</div>
                        <div class="fw-bold">${{ number_format($bill->amount, 2) }}</div>
                    </div>
                    <div class="total-row">
                        <div class="text-gray text-small">Total:</div>
                        <div class="fw-bold">${{ number_format($bill->amount, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="right-section">
                <div class="details-card">
                    <span class="badge">Approved</span>

                    <div class="details-section">
                        <h6 class="fw-bold text-gray">DETAILS</h6>
                        <div class="details-row">
                            <div class="text-gray text-small">Email:</div>
                            <div class="fw-bold">{{ $bill->customer->email }}</div>
                        </div>

                        <div class="details-row">
                            <div class="text-gray text-small">Bills Date:</div>
                            <div class="fw-bold">
                                <span class="bullet"></span>
                                {{ $bill->due_date }}
                            </div>
                        </div>
                    </div>

                    <div class="details-section">
                        <h6 class="fw-bold text-gray">PROJECT OVERVIEW</h6>
                        <div class="details-row">
                            <div class="text-gray text-small">Plan Name:</div>
                            <div class="fw-bold">{{ $bill->customerSubscription->subscriptionPlan->name }}</div>
                        </div>

                        <div class="details-row">
                            <div class="text-gray text-small">Completed By:</div>
                            <div class="fw-bold">HyperSale</div>
                        </div>

                        <div class="details-row">
                            <div class="text-gray text-small">Signatures:</div>
                            <div class="fw-bold">HyperSale</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>