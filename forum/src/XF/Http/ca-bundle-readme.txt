This CA bundle comes from cURL:

https://curl.haxx.se/docs/caextract.html

This is in turn based on the Mozilla CA certificate store.

The value found there can be used directly to replace ca-bundle.crt directly. However, to allow requests to Google services to complete successfully with OpenSSL versions prior to 1.0.2, the ca-bundle-legacy-openssl.crt file must be updated as well. This is the same as the standard ca-bundle.crt file with the contents of the legacy-equifax.crt file appended.

For details on this OpenSSL behavior and Google's certificate structure, see: https://serverfault.com/questions/841036/openssl-unable-to-get-local-issuer-certificate-for-accounts-google-com