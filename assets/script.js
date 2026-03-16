const syncBtn = document.getElementById("sync-btn");
const channelIdInput = document.getElementById("channel_id"); // make sure your input has this id
const logoutBtn = document.getElementById("logout-btn");

syncBtn.addEventListener("click", function() {
  syncChannel();  
});

logoutBtn.addEventListener("click", logout);

async function syncChannel(){
  const channelId = channelIdInput.value.trim();
  if(!channelId){
    alert("Please enter a channel ID.");
    return;
  }

  try {
    const res = await fetch('api/sync_youtube_channel.php', {
      method: 'POST',
      body: new URLSearchParams({ channel_id: channelId }),
    });
    
    // Check if user is not logged in
    if(res.status === 401){
      alert("You must login first to sync a channel!");
      window.location.href = 'auth/login.php';
      return;
    }
    
    console.log("sdas", res);
    const data = await res.json();
    if(data.error){
      console.log(data.error);
      // alert("Error: " + data.error);
      return;
    }
    
    alert(data.message || "Channel synced successfully");
    loadChannels();

  } catch(err){
    console.error(err);
    // alert("An unexpected error occurred. Please try again.");
  }
}

async function loadChannels(page = 1){
  const channelId = channelIdInput.value.trim();
  const profilePicture = document.getElementById("profile-picture");
  const channelName = document.getElementById("channel-name");
  const channelDesc = document.getElementById("channel-description");
  const videoSection = document.querySelector(".video-section");
  try {
    const res = await fetch(`api/youtube_channel_json.php?channel_id=${channelId}&page=${page}`);

    if(res.status === 401){
      alert("You must login first to view channels!");
      window.location.href = 'auth/login.php';
      return;
    }

    const data = await res.json();
    console.log(data);
    profilePicture.innerHTML = `<img src="${data.channel.thumbnail}" alt="Profile Picture" style="width: 200px; height: 200px;">`;
    channelName.textContent = data.channel.title;
    channelDesc.textContent = data.channel.description

    // Clear previous videos
   videoSection.innerHTML = "";

    data.videos.forEach(video => {
      const card = document.createElement("div");
      card.classList.add("video-card");
      card.innerHTML = `
        <img src="${video.thumbnail}" alt="${video.title}">
        <div class="video-info">
          <h3>${video.title}</h3>
          <p>${video.description.substring(0, 80)}...</p>
        </div>
      `;
      videoSection.appendChild(card);
    });

    // Pagination
    currentPage = data.page;
    totalPages = data.total_pages;
    document.getElementById("page-info").textContent = `Page ${currentPage} of ${totalPages}`;
    document.getElementById("prev-btn").disabled = currentPage <= 1;
    document.getElementById("next-btn").disabled = currentPage >= totalPages;

  } catch(err){
    console.error(err);
  }
}

// Pagination buttons
document.getElementById("prev-btn").addEventListener("click", () => {
  if(currentPage > 1) loadChannels(currentPage - 1);
});

document.getElementById("next-btn").addEventListener("click", () => {
  if(currentPage < totalPages) loadChannels(currentPage + 1);
});

function logout(){
  window.location.href = 'auth/logout.php';
  alert("You have been logged out successfully!");
}