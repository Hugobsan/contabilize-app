const Ziggy = {
    url: "http://localhost:8000",
    port: 8000,
    defaults: {},
    routes: {
        "profile.show": { uri: "user/profile", methods: ["GET", "HEAD"] },
        "other-browser-sessions.destroy": {
            uri: "user/other-browser-sessions",
            methods: ["DELETE"],
        },
        "current-user-photo.destroy": {
            uri: "user/profile-photo",
            methods: ["DELETE"],
        },
        "current-user.destroy": { uri: "user", methods: ["DELETE"] },
        "sanctum.csrf-cookie": {
            uri: "sanctum/csrf-cookie",
            methods: ["GET", "HEAD"],
        },
        login: { uri: "login", methods: ["GET", "HEAD"] },
        "auth.login": { uri: "login", methods: ["POST"] },
        "accounts-payable.index": {
            uri: "accounts-payable",
            methods: ["GET", "HEAD"],
        },
        "accounts-payable.create": {
            uri: "accounts-payable/create",
            methods: ["GET", "HEAD"],
        },
        "accounts-payable.store": {
            uri: "accounts-payable",
            methods: ["POST"],
        },
        "accounts-payable.show": {
            uri: "accounts-payable/{accounts_payable}",
            methods: ["GET", "HEAD"],
            parameters: ["accounts_payable"],
        },
        "accounts-payable.edit": {
            uri: "accounts-payable/{accounts_payable}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["accounts_payable"],
        },
        "accounts-payable.update": {
            uri: "accounts-payable/{accounts_payable}",
            methods: ["PUT", "PATCH"],
            parameters: ["accounts_payable"],
        },
        "accounts-payable.destroy": {
            uri: "accounts-payable/{accounts_payable}",
            methods: ["DELETE"],
            parameters: ["accounts_payable"],
        },
        "accounts-receivable.index": {
            uri: "accounts-receivable",
            methods: ["GET", "HEAD"],
        },
        "accounts-receivable.create": {
            uri: "accounts-receivable/create",
            methods: ["GET", "HEAD"],
        },
        "accounts-receivable.store": {
            uri: "accounts-receivable",
            methods: ["POST"],
        },
        "accounts-receivable.show": {
            uri: "accounts-receivable/{accounts_receivable}",
            methods: ["GET", "HEAD"],
            parameters: ["accounts_receivable"],
        },
        "accounts-receivable.edit": {
            uri: "accounts-receivable/{accounts_receivable}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["accounts_receivable"],
        },
        "accounts-receivable.update": {
            uri: "accounts-receivable/{accounts_receivable}",
            methods: ["PUT", "PATCH"],
            parameters: ["accounts_receivable"],
        },
        "accounts-receivable.destroy": {
            uri: "accounts-receivable/{accounts_receivable}",
            methods: ["DELETE"],
            parameters: ["accounts_receivable"],
        },
        "credit-cards.index": { uri: "credit-cards", methods: ["GET", "HEAD"] },
        "credit-cards.create": {
            uri: "credit-cards/create",
            methods: ["GET", "HEAD"],
        },
        "credit-cards.store": { uri: "credit-cards", methods: ["POST"] },
        "credit-cards.show": {
            uri: "credit-cards/{credit_card}",
            methods: ["GET", "HEAD"],
            parameters: ["credit_card"],
        },
        "credit-cards.edit": {
            uri: "credit-cards/{credit_card}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["credit_card"],
        },
        "credit-cards.update": {
            uri: "credit-cards/{credit_card}",
            methods: ["PUT", "PATCH"],
            parameters: ["credit_card"],
        },
        "credit-cards.destroy": {
            uri: "credit-cards/{credit_card}",
            methods: ["DELETE"],
            parameters: ["credit_card"],
        },
        "credit-card-purchases.index": {
            uri: "credit-card-purchases",
            methods: ["GET", "HEAD"],
        },
        "credit-card-purchases.create": {
            uri: "credit-card-purchases/create",
            methods: ["GET", "HEAD"],
        },
        "credit-card-purchases.store": {
            uri: "credit-card-purchases",
            methods: ["POST"],
        },
        "credit-card-purchases.show": {
            uri: "credit-card-purchases/{credit_card_purchase}",
            methods: ["GET", "HEAD"],
            parameters: ["credit_card_purchase"],
        },
        "credit-card-purchases.edit": {
            uri: "credit-card-purchases/{credit_card_purchase}/edit",
            methods: ["GET", "HEAD"],
            parameters: ["credit_card_purchase"],
        },
        "credit-card-purchases.update": {
            uri: "credit-card-purchases/{credit_card_purchase}",
            methods: ["PUT", "PATCH"],
            parameters: ["credit_card_purchase"],
        },
        "credit-card-purchases.destroy": {
            uri: "credit-card-purchases/{credit_card_purchase}",
            methods: ["DELETE"],
            parameters: ["credit_card_purchase"],
        },
        "purchase-installments.show": {
            uri: "purchase-installments/{purchase_installment}",
            methods: ["GET", "HEAD"],
            parameters: ["purchase_installment"],
        },
        "purchase-installments.destroy": {
            uri: "purchase-installments/{purchase_installment}",
            methods: ["DELETE"],
            parameters: ["purchase_installment"],
        },
        dashboard: { uri: "dashboard", methods: ["GET", "HEAD"] },
        "dashboard.pdf": { uri: "dashboard/pdf", methods: ["GET", "HEAD"] },
        logout: { uri: "logout", methods: ["GET", "HEAD"] },
        "storage.local": {
            uri: "storage/{path}",
            methods: ["GET", "HEAD"],
            wheres: { path: ".*" },
            parameters: ["path"],
        },
    },
};
if (typeof window !== "undefined" && typeof window.Ziggy !== "undefined") {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}
export { Ziggy };
