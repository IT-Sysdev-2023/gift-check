<template>
    <div class="card">
        <div class="header">
            <div class="image">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <g stroke-width="0" id="SVGRepo_bgCarrier"></g>
                    <g stroke-linejoin="round" stroke-linecap="round" id="SVGRepo_tracerCarrier"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" stroke="#000000"
                            d="M20 7L9.00004 18L3.99994 13"></path>
                    </g>
                </svg>
            </div>
            <div class="content">
                <span class="title">{{ title }}</span>
                <!-- <p class="message">List of Transactions needs to be eod</p> -->
            </div>
            <div class="actions">

                <button block type="button" class="history" @click="() => $inertia.get(route('accounting.pending.index'))">
                    <span>
                        <LogoutOutlined /> Pending Request
                    </span>
                    &nbsp;
                    <a-badge :count="count?.pending" show-zero>
                    </a-badge>
                </button>
                <button type="button" class="track" @click="() => $inertia.visit(route('custodian.approved.request'))">
                    <span>
                        <LogoutOutlined />
                        Approved Request
                    </span>
                </button>

                <button type="button" class="track" @click="() => $inertia.get(route('accounting.payment.payment.gc'))">
                    <span>
                        <LogoutOutlined />
                        Payment GC
                    </span>
                </button>
                <button type="button" class="track" @click="() => $inertia.get(route('accounting.payment.viewing'))">
                    <span>
                        <LogoutOutlined />
                        Payment Viewing
                    </span>
                </button>
                <!-- <button type="button" class="track" @click="() => $inertia.get(route('eod.verified.gc'))">
                    <span>
                        <LogoutOutlined />
                        Cancelled Request
                    </span>
                </button> -->
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    title: String,
    count: Object
});
</script>

<style scoped>
/* From Uiverse.io by ZstarPanda0210 */
.card {
    overflow: hidden;
    position: relative;
    text-align: left;
    border-radius: 0.5rem;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
    background-color: #fff;
}

.dismiss {
    position: absolute;
    right: 10px;
    top: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    background-color: #fff;
    color: black;
    border: 2px solid #d1d5db;
    font-size: 1rem;
    font-weight: 300;
    width: 30px;
    height: 30px;
    border-radius: 7px;
    transition: 0.3s ease;
}

.dismiss:hover {
    background-color: #ee0d0d;
    border: 2px solid #ee0d0d;
    color: #fff;
}

.header {
    padding: 1.25rem 1rem 1rem 1rem;
}

.image {
    display: flex;
    margin-left: auto;
    margin-right: auto;
    background-color: #e2feee;
    flex-shrink: 0;
    justify-content: center;
    align-items: center;
    width: 3rem;
    height: 3rem;
    border-radius: 9999px;
    animation: animate 0.6s linear alternate-reverse infinite;
    transition: 0.6s ease;
}

.image svg {
    color: #0afa2a;
    width: 2rem;
    height: 2rem;
}

.content {
    margin-top: 0.75rem;
    text-align: center;
}

.title {
    color: #066e29;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.5rem;
}

.message {
    margin-top: 0.5rem;
    color: #595b5f;
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.actions {
    margin: 0.75rem 1rem;
}

.history {
    display: inline-flex;
    padding: 0.5rem 1rem;
    background-color: #a02a1a;
    color: #ffffff;
    font-size: 1rem;
    line-height: 1.5rem;
    font-weight: 500;
    justify-content: center;
    border-radius: 0.375rem;
    border: none;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    width: 100%;
}

button {
    cursor: pointer;
}

.track {
    display: inline-flex;
    margin-top: 0.75rem;
    padding: 0.5rem 1rem;
    color: #242525;
    font-size: 1rem;
    line-height: 1.5rem;
    font-weight: 500;
    justify-content: center;
    width: 100%;
    border-radius: 0.375rem;
    border: 1px solid #d1d5db;
    background-color: #fff;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

@keyframes animate {
    from {
        transform: scale(1);
    }

    to {
        transform: scale(1.09);
    }
}
</style>
