const app = require("express")();
const cors = require('cors');
const Stream = require('node-rtsp-stream');

app.use(cors());
const streams = {};

const streams_configs = [
    {
        key: 'bunnyvideo',
        port: 9000,
        url: 'rtsp://888888:888888@192.168.1.38:554?channel=1',
    },
];

const stopStream = (port) => {
    if(streams[port]){
        streams[port].stop();
        streams[port] = null;
    }
};

const startStream = (name, streamUrl, wsPort) => {
    const stream = new Stream({
        name,
        streamUrl,
        wsPort,
        ffmpegOptions: { 
          "-stats": "", 
          "-r": 30,
        },
      });

      streams[wsPort] = stream;
}

app.get('/start-stream', (req, res)=>{

    const {url, port, key = 'stream'} = req.query;
    if(!url && !port){
        return res.json({
            message: "Bad input",
        });
    }

    if(streams[port]){
        return res.json({
            message: "Port is in use",
        });
    }

    startStream(key, url, port);

    res.json({
        message: "Started Stream",
    });
});

app.get('/stop-stream', (req, res)=>{
    const {port} = req.query;

    if (!streams[port]){
        return res.json({
            message: "Port is not in use",
        });
    }
    stopStream(port);

    returnres.json({
        message: "Stopped Stream",
    });
});

app.listen(8080, ()=> {
    console.log('Server running 8080')
    streams_configs.forEach((config)=>{
        startStream(config.key, config.url, config.port);
    });
});