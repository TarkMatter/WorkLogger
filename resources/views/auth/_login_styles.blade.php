<style>
    :root{
        --brand-1:#0a58ca;
        --brand-2:#2b6cff;
        --brand-3:#0b1b5a;
    }

    body{
        min-height:100vh;
        background:
            radial-gradient(1200px 700px at 10% 10%, rgba(43,108,255,.55), transparent 55%),
            radial-gradient(900px 600px at 90% 20%, rgba(10,88,202,.55), transparent 60%),
            radial-gradient(900px 700px at 60% 90%, rgba(11,27,90,.75), transparent 60%),
            linear-gradient(135deg, #071233 0%, #0a1a4a 40%, #08102d 100%);
    }

    .glass-card{
        background: rgba(255,255,255,.90);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,.35);
    }

    .btn-brand{
        background: linear-gradient(135deg, var(--brand-2), var(--brand-1));
        border: 0;
        box-shadow: 0 14px 30px rgba(10,88,202,.28);
    }
    .btn-brand:hover{ filter: brightness(.96); transform: translateY(-1px); }
    .btn-brand:active{ transform: translateY(0); }

    .form-control:focus, .form-check-input:focus{
        border-color: rgba(43,108,255,.55);
        box-shadow: 0 0 0 .25rem rgba(43,108,255,.20);
    }
</style>
