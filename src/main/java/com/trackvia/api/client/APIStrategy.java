/**
 * 
 */
package com.trackvia.api.client;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

/**
 * @author toddbenge
 *
 */
public abstract class APIStrategy {
	

	protected String accessToken = new String();
	protected String baseUrl = "https://api.trackvia.com";
	protected String payload = null;

	protected abstract AuthorizationRequestUrl getRequestBuilder(String clientId);
	
	protected abstract void postExecute(String responseBody);
	
	public String getAccessToken() {
		return accessToken;
	}

	protected void setAccessToken(String accessToken) {
		this.accessToken = accessToken;
	}
	
	protected void setPayload(String payload) {
		this.payload = payload;
	}
	
	public String getPayload() {
		return this.payload;
	}
	
}
