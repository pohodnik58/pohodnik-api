const path = require('path');
const https = require('https');

const FtpDeploy = require('ftp-deploy');
const zipFolder = require('zip-folder');
require('dotenv').config({ path: path.join(__dirname, './.env') })
const ftpDeploy = new FtpDeploy();

const today = new Date().toISOString().substr(0,10);
const filename = `back_${today}.zip`;

const config = {
    user: process.env.FTP_USER || 'deploy@pohodnik.tk', // NOTE that this was username in 1.x
    password: process.env.FTP_PSWD || null, // optional, prompted if none given
    host: process.env.FTP_HOST || 'ftp.fednik.ru',
    port: 21,
    localRoot: path.join(__dirname, '../tmp'),
    remoteRoot: '/deploy/',
    // include: ['*', '**/*'],      // this would upload everything except dot files
    include: [filename],
    // exclude: ['dist/**/*.map'], // e.g. exclude sourcemaps - ** exclude: [] if nothing to exclude **
    // deleteRemote: true, // delete ALL existing files at destination before uploading, if true
    forcePasv: true // Passive mode is forced (EPSV command is not sent)
};

zipFolder(path.join(__dirname, '../www/pohodnik.tk'), path.join(__dirname, `../tmp/${filename}`), function(err) {
    if(err) {
        console.log('oh no!', err);
        return;
    }

    // use with promises
    ftpDeploy.deploy(config)
        .then(res => {
            console.log('finished:', res);
            https.get(`https://pohodnik.tk/unzip.php?name=${filename}`, () => {
                console.log(`UNZIP ${filename} DONE`);
            }).on('error', err => {
                console.log(`Error: ${err.message}`);
            });

        })
        .catch(err => console.log(err));

    ftpDeploy.on('uploading', data => {
        console.log('Transfer ', data.transferredFileCount, ' from ', data.totalFilesCount, ' current ', data.filename);
    });
    ftpDeploy.on('uploaded', data => {
        console.log(data); // same data as uploading event
    });
    ftpDeploy.on('log', data => {
        console.log(data); // same data as uploading event
    });

    ftpDeploy.on('upload-error', data => {
        console.log(data.err); // data will also include filename, relativePath, and other goodies
    });

});


