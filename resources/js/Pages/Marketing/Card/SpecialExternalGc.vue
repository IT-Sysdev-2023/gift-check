<template>
  <a-card title="Special External GC Request">
    <div class="cards">
      <div class="card red">
        <a-row class="w-full">
          <a-col :span="18">
            <div class="flex justify-end">
              <p class="tip">Pending Request(GC Holder Entry)</p>
            </div>
          </a-col>
          <a-col :span="4">
            <div class="">
              <p class="tip">1</p>
            </div>
          </a-col>
        </a-row>
      </div>
      <div class="card blue">
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
              <p class="tip">2</p>
            </div>
          </a-col>
        </a-row>
      </div>
      <div class="card black">
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
              <p class="tip">2</p>
            </div>
          </a-col>
        </a-row>
      </div>
    </div>
  </a-card>
</template>

<script>
export default {

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
