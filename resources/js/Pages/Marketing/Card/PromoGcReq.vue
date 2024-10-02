<template>
    <a-card title="Promo GC Request">
        <div class="cards">
            <div @click="() => $inertia.get(route('marketing.promoGcRequest.pending.list'))" class="card red">
                <a-row class="w-full">
                    <a-col :span="4">
                    </a-col>
                    <a-col :span="12">
                        <div  class="flex justify-end">
                            <p class="tip">Pending Request</p>
                        </div>
                    </a-col>
                    <a-col :span="6">
                        <div class="flex justify-end">
                            <p class="tip">{{ countPromoGcRequest.pendingRequest }}</p>
                        </div>
                    </a-col>
                </a-row>
            </div>
            <div class="card blue">
                <div @click="() => $inertia.get(route('marketing.promoGcRequest.approved.list'))" class="card blue">
                    <a-row class="w-full">
                        <a-col :span="4">
                        </a-col>
                        <a-col :span="12">
                            <div class="flex justify-end">
                                <p class="tip">Approved Request</p>
                            </div>
                        </a-col>
                        <a-col :span="6">
                            <div class="flex justify-end">
                                <p class="tip">{{ countPromoGcRequest.approvedRequest }}</p>
                            </div>
                        </a-col>
                    </a-row>
                </div>
            </div>
            <div class="card black" @click="() => $inertia.get(route('marketing.promoGcRequest.cancelled.list'))">
                <a-row class="w-full">
                    <a-col :span="4">
                    </a-col>
                    <a-col :span="12">
                        <div class="flex justify-end">
                            <p class="tip">Cancelled Request</p>
                        </div>
                    </a-col>
                    <a-col :span="6">
                        <div class="flex justify-end">
                            <p class="tip">{{ countPromoGcRequest.cancelledRequest }}</p>
                        </div>
                    </a-col>
                </a-row>
            </div>
        </div>
    </a-card>
</template>


<script>
export default {
    props: {
        countPromoGcRequest: Object
    },
}
</script>


<style scoped>
.cards {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.cards .red {
    background-color: #f43f5e;
}

.cards .blue {
    background-color: #3b82f6;
}

.cards .black {
    background-color: #191b1a;
}

.cards .card {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    height: 30px;
    width: 100%;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    transition: 400ms;
}

.cards .card p.tip {
    font-size: 1em;
    font-weight: 700;
}

.cards .card p.second-text {
    font-size: .7em;
}

.cards .card:hover {
    transform: scale(1.1, 1.1);
}

.cards:hover>.card:not(:hover) {
    transform: scale(0.9, 0.9);
}
</style>