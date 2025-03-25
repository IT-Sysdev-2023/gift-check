<template>
    <div class="flex flex-wrap gap-10">
    <div class="border border-gray-300 rounded-lg p-4 shadow-md bg-white">
    <a-descriptions :column="1"title="Approved Budget Request Info" bordered>
          <a-descriptions-item label="BR No.":span="1">{{ data.br_no}}</a-descriptions-item>

          <a-descriptions-item label="Date Requested">{{data.br_requested_at }}</a-descriptions-item>
      
        <!-- <a-descriptions-item label="Time Requested"class="w-1/2">{{ }}</a-descriptions-item> -->
       
          <a-descriptions-item label="Budget Requested" >{{ data.br_request }}</a-descriptions-item>
      
          <a-descriptions-item label="Request Document" v-if="data.br_file_docno">
            <a-image
                style="height: 100px"
                :src="data.br_file_docno"
                :fallback="imageUrl"
            />
        </a-descriptions-item >
        
            <a-descriptions-item label="Request Remark">{{ data.br_remarks}}</a-descriptions-item>
       
            <a-descriptions-item label="Prepared By">{{ data.prepared_by.full_name }}</a-descriptions-item>
        
        </a-descriptions>
    </div>
    
    <div class="flex flex-col gap-4">
        <div class="border border-gray-300 rounded-lg p-4 shadow-md bg-white">
            <a-descriptions bordered :column="1" title="Approved Budget Details" > 
            <!-- <a-descriptions bordered style="margin-top: 20px" :column="1"> -->
        
            <a-descriptions-item label="Date Approved">{{data.abr?.approved_at }}</a-descriptions-item>

            <a-descriptions-item label="Approved Document" v-if="data.abr?.file_doc_no">
            <a-image
                style="height: 50px"
                :src="data.abr?.file_doc_no"
                :fallback="imageUrl"
            />
        </a-descriptions-item>
        <a-descriptions-item label="Approved Remarks"
            ><a-tag color="cyan">{{
                data.abr?.budget_remark
            }}</a-tag></a-descriptions-item
        >
        <a-descriptions-item label="Approved By" :span="2">{{  data.abr?.approved_by }}</a-descriptions-item>
        <a-descriptions-item label="Checked By">{{ data.abr?.checked_by}}</a-descriptions-item>
        <a-descriptions-item label="Prepared By">{{data.abr?.user_prepared_by.full_name }}</a-descriptions-item>
      </a-descriptions>
    </div>
   </div>
  </div>
</template>

<script>
import { imageFallback } from "@/Mixin/UiUtilities";
export default {
    props: {
        data: Object,
    },
    data() {
        return {
            imageUrl: imageFallback(), // Placeholder for the image URL
        };
    },
};
</script>
