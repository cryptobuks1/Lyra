<template>
  <div :class="card.class" class="px-2">
    <div class="card">
      <div class="card-body p-3">
        <h5 class="card-title mb-2 text-center">{{ card.title }}</h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item px-2" v-for="(element, key) in card.value">
            <div class="d-flex w-100 justify-content-between align-items-center">
              <span class="element-name" v-if="!element.link">{{ element.name }}</span>
              <router-link class="element-name" :to="element.link" v-else>{{ element.name }}</router-link>
              <small class="element-value">{{ formatValue(element.value) }} {{ card.suffix }}</small>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: ['card'],
    methods: {
      numberValue(value) {
        const SUFFIXES = "kMGTPEZY";
        let i = -1;
        while ((value = value / 1000) >= 1) {
          i++;
        }
        if (i === -1) return (value * 1000);
        return (value * 1000).toFixed(2) + SUFFIXES[i];
      },
      formatValue(value) {
        if (!isNaN(value)) return this.numberValue(value);
        return value;
      },
    }
  }
</script>

<style scoped>
  .card .list-group-item .element-value {
    white-space: nowrap;
  }

  .card .list-group-item .element-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 15px;
  }
</style>
