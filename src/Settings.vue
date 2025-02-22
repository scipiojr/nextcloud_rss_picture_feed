<template>
  <div class="rss-settings">
    <h1 class="text-xl font-bold mb-4">RSS Picture Feed Settings</h1>
    <div class="mt-4">
      <button @click="addFeed" class="btn">Add New RSS Feed</button>
    </div>
    <div v-for="(feed, index) in feeds" :key="feed.uuid" class="feed-settings mt-6 border p-4 rounded-lg">
      <h2 class="text-lg font-semibold">Feed {{ index + 1 }}</h2>
      
      <fieldset class="border rounded p-3 mt-4">
        <legend class="font-semibold">Basic Settings</legend>
        <label>RSS Title:</label>
        <input v-model="feed.rssTitle" type="text" class="input" />
        <label>UUID:</label>
        <input v-model="feed.uuid" type="text" class="input" readonly />
        <button @click="regenerateUuid(feed)" class="btn">Regenerate UUID</button>
        <label>Select Image Folder:</label>
        <input v-model="feed.folder" type="text" class="input" readonly />
        <button @click="selectFolder(feed)" class="btn">Choose Folder</button>
        <label>RSS TTL (minutes):</label>
        <input v-model.number="feed.ttl" type="number" class="input" min="1" required />
        <label>Allowed Extensions (comma-separated):</label>
        <input v-model="feed.allowedExtensions" type="text" class="input" required />
      </fieldset>
      
      <fieldset class="border rounded p-3 mt-4">
        <legend class="font-semibold">Feed Restrictions</legend>
        <label>Allowed IP Ranges (comma-separated):</label>
        <input v-model="feed.allowedIps" type="text" class="input" required />
        <label>Set Password (Optional):</label>
        <input v-model="feed.password" type="password" class="input" />
      </fieldset>
      
      <fieldset class="border rounded p-3 mt-4">
        <legend class="font-semibold">Image Processing</legend>
        <label>Max Image Width:</label>
        <input v-model.number="feed.maxWidth" type="number" class="input" min="100" required />
        <label>Max Image Height:</label>
        <input v-model.number="feed.maxHeight" type="number" min="100" class="input" required />
      </fieldset>
      
      <fieldset class="border rounded p-3 mt-4">
        <legend class="font-semibold">Boost New Images</legend>
        <label>New Image Boost Factor:</label>
        <input v-model.number="feed.newImageBoost" type="number" min="1" step="0.1" class="input" required />
      </fieldset>
      
      <fieldset class="border rounded p-3 mt-4">
        <legend class="font-semibold">Weight Configuration</legend>
        <label>Days Before Seasonal Boost:</label>
        <input v-model.number="feed.daysBefore" type="number" min="0" class="input" required />
        <label>Days After Seasonal Boost:</label>
        <input v-model.number="feed.daysAfter" type="number" min="0" class="input" required />
        <label>Seasonal Boost Factor:</label>
        <input v-model.number="feed.seasonalBoost" type="number" min="1" step="0.1" class="input" required />
      </fieldset>
      
      <button @click="saveFeed(index)" class="btn mt-4">Save Feed Settings</button>
      <button @click="removeFeed(index)" class="btn mt-4 bg-red-500">Remove Feed</button>
      <button @click="viewMetrics(feed.uuid)" class="btn mt-4 bg-blue-500">View Metrics</button>
      <p v-if="feed.feedback" class="mt-2 text-green-500">{{ feed.feedback }}</p>
    </div>
  </div>

  <div v-if="selectedFeed" class="metrics-container mt-8 border p-4 rounded-lg">
    <h2 class="text-lg font-semibold">Metrics for {{ selectedFeed.title }}</h2>
    <table class="w-full mt-4 border">
      <thead>
        <tr>
          <th class="border p-2">Thumbnail</th>
          <th class="border p-2">Image Name</th>
          <th class="border p-2">Views</th>
          <th class="border p-2">Last Seen</th>
          <th class="border p-2">EXIF/IPTC Date</th>
          <th class="border p-2">Weight</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="image in metrics" :key="image.path">
          <td class="border p-2"><img :src="image.thumbnail" class="w-12 h-12" /></td>
          <td class="border p-2">{{ image.name }}</td>
          <td class="border p-2">{{ image.views }}</td>
          <td class="border p-2">{{ image.lastSeen }}</td>
          <td class="border p-2">{{ image.date }}</td>
          <td class="border p-2">{{ image.weight }}</td>
        </tr>
      </tbody>
    </table>
    <button @click="resetMetrics(selectedFeed.uuid)" class="btn mt-4 bg-red-500">Reset Metrics</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      feeds: [],
      selectedFeed: null,
      metrics: []
    };
  },
  methods: {
    viewMetrics(uuid) {
      fetch(`/apps/rss_picture_feed/get-metrics/${uuid}`)
        .then(response => response.json())
        .then(data => {
          this.metrics = data.metrics;
          this.selectedFeed = this.feeds.find(feed => feed.uuid === uuid);
        });
    },
    resetMetrics(uuid) {
      fetch(`/apps/rss_picture_feed/reset-metrics/${uuid}`, { method: 'POST' })
        .then(() => {
          this.metrics = [];
        });
    }
  }
};
</script>
