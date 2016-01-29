package com.trackvia.api.client;

import java.io.IOException;

import javax.net.ssl.SSLContext;
import javax.net.ssl.SSLException;
import javax.net.ssl.SSLSession;
import javax.net.ssl.SSLSocket;
import javax.net.ssl.TrustManager;
import javax.net.ssl.X509TrustManager;
import org.apache.http.client.HttpClient;
import org.apache.http.conn.ClientConnectionManager;
import org.apache.http.conn.scheme.Scheme;
import org.apache.http.conn.scheme.SchemeRegistry;
import org.apache.http.conn.ssl.SSLSocketFactory;
import org.apache.http.conn.ssl.X509HostnameVerifier;
import org.apache.http.impl.client.DefaultHttpClient;

	
	/*
	This code is public domain: you are free to use, link and/or modify it in any way you want, for all purposes including commercial applications. 
	*/
	public class WebClientWrapper {
	 
	    public static HttpClient wrapClient(HttpClient base) {
	        try {
	            SSLContext ctx = SSLContext.getInstance("TLS");
	            X509TrustManager tm = new X509TrustManager() {
	

					public void checkClientTrusted(java.security.cert.X509Certificate[] chain,String authType)  throws java.security.cert.CertificateException {
						
					}

					public void checkServerTrusted(java.security.cert.X509Certificate[] chain,String authType) throws java.security.cert.CertificateException {
					}

					public java.security.cert.X509Certificate[] getAcceptedIssuers() {
						// TODO Auto-generated method stub
						return null;
					}

	
	            };
	            X509HostnameVerifier verifier = new X509HostnameVerifier() {

					public boolean verify(String arg0, SSLSession arg1) {
						// TODO Auto-generated method stub
						return false;
					}

					public void verify(String arg0, SSLSocket arg1)
							throws IOException {
						// TODO Auto-generated method stub
						
					}

					public void verify(String arg0,
							java.security.cert.X509Certificate arg1)
							throws SSLException {
						// TODO Auto-generated method stub
						
					}

					public void verify(String arg0, String[] arg1, String[] arg2)
							throws SSLException {
						// TODO Auto-generated method stub
						
					}
	 
	              
	            };
	            ctx.init(null, new TrustManager[]{tm}, null);
	            SSLSocketFactory ssf = new SSLSocketFactory(ctx);
	            ssf.setHostnameVerifier(verifier);
	            ClientConnectionManager ccm = base.getConnectionManager();
	            SchemeRegistry sr = ccm.getSchemeRegistry();
	            sr.register(new Scheme("https", ssf, 443));
	            return new DefaultHttpClient(ccm, base.getParams());
	        } catch (Exception ex) {
	            ex.printStackTrace();
	            return null;
	        }
	    }
	}

