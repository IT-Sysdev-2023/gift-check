<template>
    <a-result status="success" title="Successfully Generating Gift Check Layout!"
        sub-title="Please click the print button to redirect in to the print.">
        <template #extra>
            <a-button type="primary" @click="printDiv">
                <template #icon>
                    <FastForwardOutlined />
                </template>
                Print Gift Check
            </a-button>
            <a-button @click="() => $inertia.get(route(url))">
                <template #icon>
                    <FastForwardOutlined />
                </template>
                Back to Setup
            </a-button>
        </template>
        <div id="printableArea">
            <div v-for="item in record">
                <div class="gift-check">
                    <span class="agcSpan">
                        {{ item.custname }}
                    </span>
                    <span class="agcSpanHolder">
                        {{ item.holder }}
                    </span>
                    <span class="denomSpan" v-if="item?.spexgcemp_denom">
                        {{ item.spexgcemp_denom }}
                    </span>
                    <span class="denomSpan" v-if="item?.dti_denom">
                        {{ item.dti_denom }}
                    </span>
                    <span class="todaySpan">
                        {{ today }}
                    </span>
                    <span class="numword">
                        {{ item.numWords }}
                    </span>
                    <img class="img" src="../../../../../public/images/gcimages/bdo_gc2.jpg" alt="">
                    <div class="barcode">
                        <img :src="'data:image/png;base64,' + item.barcode" />
                        <span class="barcodeSpan" v-if="item.spexgcemp_barcode">
                            {{ item.spexgcemp_barcode }}
                        </span>
                        <span class="barcodeSpan" v-if="item.dti_barcode">
                            {{ item.dti_barcode }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a-result>
</template>


<script>
import dayjs from 'dayjs';

export default {
    props: {
        record: Object,
        url: String
    },
    data() {
        return {
            today: dayjs().format('MMM, DD YYYY')
        }
    },
    methods: {
        printDiv() {
            const printContents = document.getElementById('printableArea').innerHTML;
            const originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }
    },
}
</script>

<style scoped>
.gift-check {
    position: relative;
    display: inline-block;
}

.img {
    width: 690px;
    height: 250px;
    margin-bottom: 25px;
    border: 1px solid #000;
}

.barcodeSpan {
    font-size: 10px;
    letter-spacing: 1px;
    top: -25%;
    right: -50%;
    position: relative;

}

.todaySpan {
    font-size: 12px;
    letter-spacing: 0.5px;
    font-weight: 600;
    top: 16%;
    right: 14%;
    position: absolute;
}

.agcSpan {
    text-align: center;
    width: 360px;
    font-size: 12px;
    letter-spacing: 0.5px;
    font-weight: bold;
    top: 22.8%;
    right: 27.6%;
    position: absolute;
}

.agcSpanHolder {
    text-align: center;
    width: 390px;
    font-size: 12px;
    letter-spacing: 0.5px;
    font-weight: bold;
    top: 30.8%;
    right: 33.6%;
    position: absolute;
}

.denomSpan {
    font-size: 12px;
    letter-spacing: 0.5px;
    font-weight: bold;
    top: 51.5%;
    right: 60.5%;
    position: absolute;
}

.numword {
    font-size: 12px;
    letter-spacing: -0.5px;
    font-weight: bold;
    top: 51.5%;
    right: 71.5%;
    position: absolute;
}

.barcode {
    width: 200px;
    height: 30px;
    position: absolute;
    right: -9.7%;
    top: 49%;
    transform: translate(0, -44%) rotate(-90deg);
    margin-right: 10px;
    /* Optional: to add some spacing from the edge */
}

.barcode img {
    height: 40px;
    width: 230px;
}
</style>
