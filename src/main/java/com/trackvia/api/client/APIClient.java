package com.trackvia.api.client;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.HttpResponseException;
import org.apache.http.client.ResponseHandler;
import org.apache.http.client.methods.HttpDelete;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.client.methods.HttpPut;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.BasicResponseHandler;
import org.apache.http.impl.client.DefaultHttpClient;

import com.fasterxml.jackson.core.JsonParseException;
import com.fasterxml.jackson.databind.JsonMappingException;
import com.fasterxml.jackson.databind.ObjectMapper;

public class APIClient {
	
	protected String clientId = new String();
	
	
	protected String accessToken = new String();
	protected String refreshToken = new String();
	
	public String getClientId() {
		return clientId;
	}

	public void setClientId(String clientId) {
		this.clientId = clientId;
	}

	public String getAccessToken() {
		return accessToken;
	}

	public void setAccessToken(String accessToken) {
		this.accessToken = accessToken;
	}

	public String getRefreshToken() {
		return refreshToken;
	}

	public void setRefreshToken(String refreshToken) {
		this.refreshToken = refreshToken;
	}

	public APIClient(String clientId) {
		this.clientId = clientId;
	}
	
	public String execute(APIStrategy strategy, String requestType) {
		
		 HttpClient client = WebClientWrapper.wrapClient(new DefaultHttpClient());
		 String responseBody = new String();

		 try {
			 	if (requestType.equals("post")) {
			 		HttpPost httprequest = new HttpPost(strategy.getRequestBuilder(getClientId()).build());
			 		System.out.println("executing request " + httprequest.getURI());
			 		if (strategy.getPayload() != null) {
			 			StringEntity input = new StringEntity(strategy.getPayload());
			 			input.setContentType("application/json");
			 			httprequest.setEntity(input);
			 		}
			 		// Create a response handler
			 		ResponseHandler<String> responseHandler = new BasicResponseHandler();
			 		responseBody = client.execute(httprequest, responseHandler);
			 	} else if (requestType.equals("put")) {
		 			HttpPut httprequest = new HttpPut(strategy.getRequestBuilder(getClientId()).build());
		 			System.out.println("executing request " + httprequest.getURI());
			 		if (strategy.getPayload() != null) {
			 			StringEntity input = new StringEntity(strategy.getPayload());
			 			input.setContentType("application/json");
			 			httprequest.setEntity(input);
			 		}		 			
		 			// Create a response handler
		 			ResponseHandler<String> responseHandler = new BasicResponseHandler();
		 			responseBody = client.execute(httprequest, responseHandler);
		 		} else if (requestType.equals("delete")) {
	 				HttpDelete httprequest = new HttpDelete(strategy.getRequestBuilder(getClientId()).build());
	 				System.out.println("executing request " + httprequest.getURI());
	 				// Create a response handler
	 				ResponseHandler<String> responseHandler = new BasicResponseHandler();
	 				responseBody = client.execute(httprequest, responseHandler);
	 			} else {
			 		HttpGet httprequest = new HttpGet(strategy.getRequestBuilder(getClientId()).build());
			 		System.out.println("executing request " + httprequest.getURI());
			 		// Create a response handler
			 		ResponseHandler<String> responseHandler = new BasicResponseHandler();
			 		responseBody = client.execute(httprequest, responseHandler);
			 	}


	            System.out.println("----------------------------------------");
	            System.out.println(responseBody);
	            System.out.println("----------------------------------------"); 
	            
	            strategy.postExecute(responseBody);

	        } catch (ClientProtocolException e) {
				// TODO Auto-generated catch block
//				e.printStackTrace();
	        	responseBody = e.getMessage();
			} catch (IOException e) {
				// TODO Auto-generated catch block
//				e.printStackTrace();
				responseBody = e.getMessage();
			} finally {
	            // When HttpClient instance is no longer needed,
	            // shut down the connection manager to ensure
	            // immediate deallocation of all system resources
	            client.getConnectionManager().shutdown();
	        }	
		 
		 return responseBody;
	}
	
	public static Map<String, Object> getResponseAsMap(String responseBody)  {
		Map<String, Object> values = new HashMap<String, Object> ();
		ObjectMapper mapper = new ObjectMapper();
		
		try {
			values = mapper.readValue(responseBody, Map.class);
		} catch (JsonParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JsonMappingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		 
		return values;
	}
	
	public static List<Object> getResponseAsList(String responseBody)  {
		List<Object> values = new ArrayList<Object> ();
		ObjectMapper mapper = new ObjectMapper();
		
		try {
			values = mapper.readValue(responseBody, List.class);
		} catch (JsonParseException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JsonMappingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		 
		return values;
	}

}
