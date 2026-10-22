// 👋 CYGNUSX Welcome & Auto-Role Bot
const { Client, GatewayIntentBits } = require('discord.js');
require('dotenv').config();

const client = new Client({ intents: [GatewayIntentBits.Guilds, GatewayIntentBits.GuildMembers] });

client.once('ready', () => {
    console.log(`[WELCOME BOT] Logged in as ${client.user.tag}! Ready to welcome members.`);
});

client.on('guildMemberAdd', (member) => {
    console.log(`[JOIN] Welcome ${member.user.tag} to the server!`);
});

client.login(process.env.DISCORD_TOKEN);
