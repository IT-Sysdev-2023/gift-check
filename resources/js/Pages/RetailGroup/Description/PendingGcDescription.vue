<template>
    <a-card>
        <p class="text-center font-bold mb-2">Promo GC Request Details</p>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="RFPROM #">{{ record.pgcreq_reqnum }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Department">{{record.title}}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Retail Group">Group {{ record.pgcreq_group }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Date Requested">{{ record.pgcreq_datereq }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Time Requested">{{ record.time }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Date Needed">{{ record.pgcreq_dateneeded }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Total GC Budget">{{ record.pgcreq_total }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Remarks">{{ record.pgcreq_remarks }}</a-descriptions-item>
        </a-descriptions>
        <a-descriptions size="small" layout="horizontal" bordered>
            <a-descriptions-item style="width: 50%;" label="Requested by">{{ record.fullname }}</a-descriptions-item>
        </a-descriptions>
    </a-card>
</template>
<script setup>
defineProps({
    record: Object
})
</script>
