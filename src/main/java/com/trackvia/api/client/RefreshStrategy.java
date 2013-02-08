package com.trackvia.api.client;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import com.google.api.client.auth.oauth2.AuthorizationRequestUrl;

public class RefreshStrategy extends LoginStrategy {

	
	
	public AuthorizationRequestUrl getRequestBuilder(String clientId) {
		// TODO Auto-generated method stub
		
		ArrayList<String> responseTypes = new ArrayList<String> ();
		responseTypes.add("token");
		
		AuthorizationRequestUrl builder = new AuthorizationRequestUrl("https://api.trackvia.com/oauth/v2/token", clientId, responseTypes);
		builder.put("client_secret", this.clientSecret);
		
		Map<String, String> params = new HashMap<String, String> ();
		params.put("grant_type", "refresh_token");
		params.put("refresh_token", getRefreshToken());
		builder.putAll(params);
	
		return builder;
	}
		

	public RefreshStrategy(String clientSecret, String refreshToken) {
		super(new String(), new String(), clientSecret);
		setRefreshToken(refreshToken);
	
	}

}
