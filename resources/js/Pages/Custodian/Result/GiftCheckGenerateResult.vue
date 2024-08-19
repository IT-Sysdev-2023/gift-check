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
            <a-button  @click="() => $inertia.get(route('custodian.approved.request'))">
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
                       ALTURAS GROUP OF COMPANIES
                    </span>
                    <span class="denomSpan">
                        {{ item.spexgcemp_denom }}
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
                        <span class="barcodeSpan">
                            {{ item.spexgcemp_barcode }}
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
        record: Object
    },
    data(){
        return {
            today: dayjs().format('DD/MM/YYYY')
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
    margin-bottom: 20px;
}
.barcodeSpan{
    font-size: 10px;
    letter-spacing: 1px;
    top: -25%;
    right: -35%;
    position: relative;
}

.todaySpan{
    font-size: 12px;
    letter-spacing: 0.5px;
    font-weight: lighter;
    top: 16%;
    right: 16.6%;
    position: absolute;
}
.agcSpan{
    font-size: 12px;
    letter-spacing: 0.5px;
    font-weight: bold;
    top: 22.8%;
    right: 43.6%;
    position: absolute;
}
.denomSpan{
    font-size: 12px;
    letter-spacing: 0.5px;
    font-weight: bold;
    top: 51.5%;
    right: 60.5%;
    position: absolute;
}
.numword{
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
    top: 50%;
    transform: translate(0, -44%) rotate(-90deg);
    margin-right: 10px;
    /* Optional: to add some spacing from the edge */
}

.barcode img {
    height: 40px;
    width: 230px;
}
</style>
